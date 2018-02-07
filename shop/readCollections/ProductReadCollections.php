<?php
namespace shop\readCollections;

use shop\entities\Shop\Product\Product;
use yii\data\ActiveDataProvider;
use yii\data\DataProviderInterface;
use shop\entities\Shop\Category;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use shop\entities\Shop\Brand;
use shop\entities\Shop\Product\Tag;

class ProductReadCollections
{
    /**
     * get list of all products
     *
     * @return DataProviderInterface
     */
    public function getAll(): DataProviderInterface
    {
        $query = Product::find()->alias('p')->active('p')->with('mainPhoto');

        return $this->getProvider($query);
    }

    /**
     * get product by 'limit'
     *
     * @param $limit
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getFeatured($limit)
    {
        return Product::find()->with('mainPhoto')->orderBy(['id' => SORT_DESC])->limit($limit)->all();
    }

    /**
     * get all products in category and subcategories
     *
     * @param Category $category
     * @return DataProviderInterface
     */
    public function getAllByCategory(Category $category): DataProviderInterface
    {
        $query = Product::find()->alias('p')->active('p')->with('mainPhoto', 'category');
        $ids = ArrayHelper::merge([$category->id], $category->getLeaves()->select('id')->column()); // get children 'ids' with NestedSetBehavior method -> 'getDescendants()'
        $query->joinWith(['categoryAssignments ca'], false);
        $query->andWhere(['or', ['p.category_id' => $ids], ['ca.category_id' => $ids]]);
        $query->groupBy('p.id');

        return $this->getProvider($query);
    }

    /**
     * get all products by brand
     *
     * @param Brand $brand
     * @return DataProviderInterface
     */
    public function getAllByBrand(Brand $brand): DataProviderInterface
    {
        $query = Product::find()->alias('p')->active('p')->with('mainPhoto');
        $query->andWhere(['p.brand_id' => $brand->id]);

        return $this->getProvider($query);
    }

    /**
     * get all products by tag
     *
     * @param Tag $tag
     * @return DataProviderInterface
     */
    public function getAllByTag(Tag $tag): DataProviderInterface
    {
        $query = Product::find()->alias('p')->active('p')->with('mainPhoto');
        $query->joinWith(['tagAssignments ta'], false);
        $query->andWhere(['ta.tag_id' => $tag->id]);
        $query->groupBy('p.id');

        return $this->getProvider($query);
    }

    /**
     * @param $id
     * @return null|Product
     */
    public function find($id): Product
    {
        return Product::find()->active()->andWhere(['id' => $id])->one();
    }

    /**
     * @param ActiveQuery $query
     * @return ActiveDataProvider
     */
    public function getProvider(ActiveQuery $query): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
                'attributes' => [
                    'id' => [
                        'asc' => ['p.id' => SORT_ASC],
                        'desc' => ['p.id' => SORT_DESC],
                    ],
                    'name' => [
                        'asc' => ['p.name' => SORT_ASC],
                        'desc' => ['p.name' => SORT_DESC],
                    ],
                    'price' => [
                        'asc' => ['p.price_new' => SORT_ASC],
                        'desc' => ['p.price_new' => SORT_DESC],
                    ],
                    'rating' => [
                        'asc' => ['p.rating' => SORT_ASC],
                        'desc' => ['p.rating' => SORT_DESC],
                    ],
                ]
            ],
            'pagination' => [
                'pageSizeLimit' => [15, 100],
            ]
        ]);
    }
}
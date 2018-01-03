<?php
namespace shop\forms\manage\Shop;

use shop\forms\CompositeForm;
use shop\validators\SlugValidator;
use yii\base\Model;
use shop\entities\Shop\Category;
use shop\forms\manage\MetaForm;

class CategoryForm extends CompositeForm
{
    public $name;
    public $slug;
    public $title;
    public $description;
    public $parentId;

    private $_categoryObj;

    /**
     * CategoryForm constructor.
     * @param Category|null $category
     * @param array $config
     */
    public function __construct(Category $category = null, array $config = [])
    {
        if($category) {
            $this->name = $category->name;
            $this->slug = $category->slug;
            $this->title = $category->title;
            $this->description = $category->description;
            $this->parentId = $category->parent ? $category->parent->id : null;

            $this->meta = new MetaForm($category->meta);
            $this->_categoryObj = $category;
        } else {
            $this->meta = new MetaForm();
        }
        parent::__construct($config);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['name', 'slug'], 'required'],
            [['parent_id'], 'integer'],
            [['name', 'slug', 'title'], 'string', 'max' => 255],
            [['description'], 'string'],
            ['slug', SlugValidator::class],
            [['name', 'slug'], 'unique', 'targetClass' => Category::class, 'filter' => $this->_categoryObj ? ['<>', 'id', $this->_categoryObj->id] : null]
        ];
    }

    /**
     * @return array
     */
    public function internalForms(): array
    {
        return ['meta'];
    }
}
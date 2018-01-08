<?php
namespace shop\forms\manage\Shop\Product;

use yii\base\Model;
use shop\entities\Shop\Product\Product;
use yii\helpers\ArrayHelper;

class TagsForm extends Model
{
    public $existing = [];
    public $textNew;

    public function __construct(Product $product = null, array $config = [])
    {
        if($product) {
            $this->existing = ArrayHelper::getColumn($product->tagAssignments, 'tag_id'); // берем по связи с тэгами по полю 'tag_id'
        }
        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            ['existing', 'each', 'rule' => ['integer']],
            ['textNew', 'string'],
        ];
    }

    /**
     * @return array
     */
    public function getNewNames(): array
    {
        return array_map('trim', preg_split('#\s*,\s*#i', $this->textNew));
    }
}
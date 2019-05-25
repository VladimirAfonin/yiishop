<?php
namespace frontend\entities;

use shop\entities\Shop\Product\Product;

class Cart
{
    private $items;

    /**
     * @param Product $product
     * @param $modId
     * @param $quantity
     */
    public function add(Product $product, $modId, $quantity)
    {
        $this->loadItems();

        $id = md5(serialize([$product->id, $modId]));

        foreach ($this->items as $k => $item) {
            if($item['id'] == $item['id']) {
                $item[$k]['quantity'] += $quantity;
                $this->saveItems($this->items);
                return;
            }
        }

        $items[] = [
            'id' => $id,
            'product' => $product,
            'modId' => $modId,
            'quantity' => $quantity,
        ];

        $this->saveItems($items);
    }

    /**
     * @param $id
     */
    public function remove($id)
    {
        $this->loadItems();

        foreach ($this->items as $k => $item) {
            if($item['id'] == $id) {
                unset($this->items[$k]);
            }
        }

        $this->saveItems($this->items);
    }

    /**
     * @param $id
     * @param $quantity
     * @throws \Exception
     */
    public function set($id, $quantity)
    {
        $this->loadItems();

        foreach ($this->items as $i => $item) {
            if ($item['id'] == $id) {
                $item[$i]['quantity'] = $quantity;
                $this->saveItems($this->items);
                return;
            }
        }
        throw new \Exception('element not found in cart.');
    }


    public function loadItems()
    {
        if($this->items === null) {
           $this->items = \Yii::$app->session->get('cart', []);
        }
    }

    /**
     * @param $items
     */
    public function saveItems($items)
    {
        \Yii::$app->session->set('cart', $this->items);
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        $this->loadItems();
        return $this->items;
    }

    /**
     * @return int
     */
    public function getCount()
    {
        $this->loadItems();
        return count($this->items);
    }

    /**
     * @return float|int
     * @throws \Exception
     */
    public function getTotalAmount()
    {
        $this->loadItems();

        $total = 0;
        foreach ($this->items as $item) {
            $product = $item['product'];
            /** @var Product $product */

            $total += $product->getModificationPrice($item['modification']) * $item['quantity'];
        }
        return $total;
    }


}
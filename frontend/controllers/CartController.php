<?php
namespace frontend\controllers;

use frontend\entities\Cart;
use shop\collections\ProductCollection;
use yii\web\Controller;
use yii\base\Module;

class CartController extends Controller
{
    private $cart;
    public $_productsRepo;

    public function __construct($id, Module $module, Cart $cart, ProductCollection $products, array $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->cart = $cart;
        $this->_productsRepo = $products;

    }

    public function actionIndex()
    {
        $items = $this->cart->getItems();

        return $this->render('index', [
            'items' => $items,
        ]);
    }

    public function actionAdd($id, $quantity)
    {
        $product = $this->_productsRepo->get($id);
        $this->cart->add($product, $modid, $quantity);

        return $this->redirect('index');
    }

    public function actionRemove($id)
    {
        $this->cart->remove($id);
        return $this->redirect('index');
    }
}
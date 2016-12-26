<?php

namespace CodeCommerce\Http\Controllers;

use Illuminate\Http\Request;

use CodeCommerce\Http\Requests;
use CodeCommerce\Http\Controllers\Controller;
use CodeCommerce\Order;
use CodeCommerce\OrderItem;
use CodeCommerce\Category;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use CodeCommerce\Events\CheckoutEvent;

class CheckoutController extends Controller
{
    private $orderModel;
    private $orderItem; 
    private $categoria;
    
    public function __construct(Order $orderModel, OrderItem $orderItem, Category $category){
        
       
       // $this->middleware('auth');
        $this->orderModel = $orderModel;
        $this->orderItem = $orderItem;
        $this->categoria = $category;
    }
    public function place(){
        if(!Session()->has('cart')){
            return false;
        }
    $cart = Session()->get('cart');
    
    //dd($cart);
    if($cart->getTotal() > 0) {
        //Passa os dados do pedido para o banco - obs. criar a fillable na model
        //order migrations
        $order = $this->orderModel->create(['user_id'=>Auth::user()->id, 'total'=>$cart->getTotal()]);
       // dd($order);
        //percorre todos os itens do carrinho
        foreach($cart->all() as $k=>$item){
            //model
          // dd($cart->all());
            $order->items()->create(['product_id'=>$k, 'price'=>$item['price'], 'qtd'=>$item['qtd']]);
        
        }
        //limpar o carrinho
      $cart->clear();
      
      event(new CheckoutEvent(Auth::user(), $order));
     
     // dd($order);
      $categories = $this->categoria->all();
     
      return view('loja.checkout', compact('order', 'categories'));
    }
        $categories = $this->categoria->all();
     
     return view('loja.checkout', ['cart'=>'empty', 'categories'=>$categories]);
    }
    
}

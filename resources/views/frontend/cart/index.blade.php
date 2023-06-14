@extends('layouts.frontend.app')
@section('content')
    <!-- Breadcrumb Begin -->
    <div class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__links">
                        <a href="{{ url('/') }}"><i class="fa fa-home"></i> Home</a>
                        <span>Cart</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <section class="shop-cart spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <form action="{{ route('cart.update') }}" method="post">
                        @csrf
                    <div class="shop__cart__table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data['carts'] as $carts)
                                    <tr>
                                        <td class="cart__product__item">
                                            <img src="{{ asset($carts->Product->thumbnails_path) }}" alt="" width="90">
                                            <div class="cart__product__item__title">
                                                <h6>{{ $carts->Product->name }}</h6>
                                            </div>
                                        </td>
                                        <td class="cart__price">{{ $carts->Product->price_rupiah }}</td>
                                        <input type="hidden" name="cart_id[]" value="{{ $carts->id }}">
                                        <td class="cart__quantity">
                                            <div>
                                                <input type="number" value="{{ $carts->qty }}" name="cart_qty[]" style="border: none;" min="0" max="{{ $carts->Product->qty }}"  oninput="checkQty(this)">
                                            </div>
                                        </td>
                                        <td class="cart__total">{{ rupiah($carts->total_price_per_product) }}</td>
                                        <td class="cart__close"><a href="{{ route('cart.delete',$carts->id) }}"><span class="icon_close"></span></a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="cart__btn">
                        <a href="{{ route('product.index') }}">Continue Shopping</a>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6">
                    <div class="cart__btn update__btn">
                        <button type="submit"><span class="icon_loading"></span> Update cart</button>
                    </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-6">
                </div>
                <div class="col-lg-4 offset-lg-2">
                    <div class="cart__total__procced">
                        <h6>Cart total</h6>
                        <ul>
                            <li>Total <span>{{ rupiah($data['carts']->sum('total_price_per_product')) }}</span></li>
                        </ul>
                        <a href="{{ route('checkout.index') }}" class="primary-btn">Proceed to checkout</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            var input = document.querySelector('input[name="cart_qty[]"]');
            checkQty(input);
            showAlert(input);
        });
        
        function checkQty(input) {
            var currentQty = parseInt(input.value);
            var maxQty = parseInt(input.getAttribute('max'));
        
            if (currentQty > maxQty) {
                input.value = maxQty;
            }
        }
        
        function showAlert(input) {
            var currentQty = parseInt(input.value);
            var maxQty = parseInt(input.getAttribute('max'));
        
            if (currentQty > maxQty) {
                var alertDiv = document.createElement('div');
                alertDiv.classList.add('alert', 'alert-danger');
                alertDiv.textContent = 'Jumlah melebihi stok yang tersedia!';
                input.parentElement.appendChild(alertDiv);
        
                setTimeout(function() {
                    alertDiv.style.display = 'none';
                }, 2000);
            }
        }
        </script>
@endsection

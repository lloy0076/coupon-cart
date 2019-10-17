@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Dashboard</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        <h2>Cart Demo</h2>
                        <p>
                            Welcome to a basic cart demo; to see the demo, go <a href="{{ route('products.index') }}">here</a>.
                        </p>

                        <p>
                            Here are some points:

                        <ul>
                            <li>There are a small number of created items; they can be used together or singly to test
                                out edge cases for the coupon rules
                            </li>
                            <li>You must be logged in (or have an account) to use the cart;</li>
                            <li>Finalising the cart doesn't do anything in particular.</li>

                        </ul>
                        </p>

                        @role(Constants::ROLE_ADMIN)
                        <h2>Admin</h2>
                        <p>
                            As an administrator you have access to the manage coupons pages, found <a
                                    href="{{ route('web.coupons') }}">here</a>.
                        </p>

                        <p>
                            Please note that the expression language used in the <b>coupon rules</b> and <b>discount
                                rules</b> are from the Symfony Expression Language component, found <a
                                    href="https://symfony.com/doc/current/components/expression_language.html">here</a>.
                        </p>

                        <p>
                            At the moment <b>only </b> the <b>cart</b> can be used as a variable in an expression,
                            however through this it is possible to access the cart's owner and many more actions. <b>Non
                                Admins</b> should not be able to construct rules as it is possible they could be abused.
                        </p>

                        <p>
                            A small number of sample rules are provided.
                        </p>
                        @endrole
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

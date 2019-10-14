<p align="center"><img src="https://res.cloudinary.com/dtfbvvkyp/image/upload/v1566331377/laravel-logolockup-cmyk-red.svg" width="400"></p>

## Coupon Cart

This is a very basic, minimally styled shopping cart application that uses:

* Laravel ^6.2
* React ^16.2
* MySQL ^5.7.19

## Cart Functionality

* Basic "customer (_user_) registration
* Ability to view items in the "store"
* Ability to manipulate a single cart:
  * Create, Retrieve, Update and Delete (_CRUD_) for the cart
* Discounts coupons, when applicable, will apply to the cart
* Ability to checkout, or finalise the cart

## Store / Admin Functionality

* The ability to configure coupons using a basic "expression language"
* The ability to set "priorities" for configured coupons _in case_ two or more coupons match
* The ability to "retire" coupons (*) and/or delete coupons

(*) Coupons are *retired* if they have one or more uses.

### Unimplemented Functionality

* The ability to add one or more items for sale; this is currently done using a database seeder for testing purposes
  * Ideas: develop an "in house system", source the items from "shopify", "etsy", "ebay", "magento" etc.
* The ability to configure the coupon rules using a "nice expression builder":
  * Ideas: make a nice, GUI to build the coupons
  
## Constraints

* We are NOT developing the full "e-commerce experience"
* Only one single coupon may be entered at one time

The instructions say:

> We are interested in how your DB schema will look like and how you'll organize your code. 
> Don't waste time with the UI.

Thus, the UI will be plain and functional.
 
## Contributing

Thank you for considering contributing to the application!

## Security Vulnerabilities

If you discover a security vulnerability within the application, please send an e-mail to [David Lloyd](jwickentower@gmail.com).

## License

This application is licensed under the [Apache 2.0 License](https://www.apache.org/licenses/LICENSE-2.0.txt).


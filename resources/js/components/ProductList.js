import React from 'react';
import ReactDOM from "react-dom";
import Spinner from "react-bootstrap/Spinner";
import Col from "react-bootstrap/Col";
import Row from "react-bootstrap/Row";
import Table from "react-bootstrap/Table";

import ProductCard from "./ProductCard";
import Form from "react-bootstrap/Form";
import Button from "react-bootstrap/Button";

/**
 * The product list.
 */
class ProductList extends React.Component {
    /**
     * The constructor.
     *
     * @param props
     */
    constructor(props) {
        super(props);
        this.state = {
            "loading":        false,
            "products":       [],
            "cart":           {},
            "applied_coupon": "",
        };
    }

    /**
     * Perform basic loading tidy ups.
     */
    startLoading() {
        this.setState({"loading": true});
        this.clearErrors();
    }

    /**
     * Stop loading.
     */
    stopLoading() {
        this.setState({"loading": false});
    }

    /**
     * Get rid of any error alerts.
     */
    clearErrors() {
        this.setState({"error": null});
    }

    /**
     * Begin a data load as soon as we are mounted.
     */
    async componentDidMount() {
        this.loadData();
    }

    /**
     * Get load from upstream.
     *
     * @returns {Promise<void>}
     */
    async loadData() {
        let uri     = "/api/products";
        let cartUri = "/api/carts";

        axios.get(cartUri).then((response) => {
            console.log(response.data);
            console.log(response.data.coupon);

            this.setState({
                "cart":           response.data,
                "applied_coupon": response.data.coupon ? response.data.coupon.coupon_code : ""
            });
            this.stopLoading();
        }).catch((error) => {
            console.error(error);
            this.stopLoading();
        });

        axios.get(uri).then((response) => {
            this.setState({"products": response.data});
            this.stopLoading();
        }).catch((error) => {
            console.error(error);
            this.stopLoading();
        });
    }

    /**
     * Add a product to the cart.
     *
     * @param event
     * @param product
     * @param quantity
     */
    addProduct(event, product, quantity) {
        let uri = "/api/cartOp/" + encodeURIComponent(product.id) + "/" + encodeURIComponent(quantity);

        axios.post(uri).then((response) => {
            this.loadData().catch((error) => console.error(error));
        }).catch((error) => {
            console.error(error);
        });
    }

    /**
     * Remove a product from the cart.
     *
     * @param event
     * @param product
     */
    removeProduct(event, product) {
        console.log("Remove Product");
        console.log(event);
        console.log(product);

        let uri = "/api/cartOp/" + encodeURIComponent(product.id);
        console.info(uri);

        axios.delete(uri).then((response) => {
            this.loadData().then((e) => {

            });
        }).catch((error) => {
            console.error(error);
        });
    }

    /**
     * Get the coupon by code.
     */
    getCouponByCode() {
        let uri = "/api/coupons/byCouponCode";
        let q   = this.state.applied_coupon || '';

        axios.get(uri, {"params": {"coupon_code": q}}).then((response) => {
            let coupon = response.data;

            let applyUri = "/api/cartOp/applyCoupon/" + encodeURIComponent(coupon.id);

            axios.get(applyUri).then((response) => {
                this.loadData().catch((error) => {
                    console.error(error);
                });
            }).catch((error) => {
                console.error(error);
            });

        }).catch((error) => {
            if (error.response.status != 404) {
            }
        });
    }

    /**
     * Removes the associated coupon.
     */
    removeCoupon(e) {
        let applyUri = "/api/cartOp/removeCoupons";

        axios.get(applyUri).then((response) => {
            this.setState({"applied_coupon": ""});

            this.loadData().catch((error) => {
                console.error(error);
            });
        }).catch((error) => {
            console.error(error);
        });
    }

    /**
     * Finalises the cart.
     */
    finaliseCart() {
        let uri = "/api/cartOp/finaliseCart/" + encodeURIComponent(this.state.cart.id);

        axios.post(uri).then((respose) => {
            this.loadData().catch((error) => {
                console.error(error);
            });
        }).catch((error) => {
            console.error(error);
        });
    }

    /**
     * Keep track of the apply coupon text box.
     *
     * @param e
     */
    handleChange(e) {
        let name = e.target.name;

        if (name === "applied_coupon") {
            this.setState({"applied_coupon": e.target.value}, () => {
                this.getCouponByCode();
            });
        } else {
            console.error(`Change for ${name} is not handled.`);
        }
    }

    /**
     * Render the item.
     *
     * @returns {*}
     */
    render() {
        let listItems = this.state.products.map((product) => {
            return (
                <Col key={product.id} lg={true}>
                    <ProductCard product={product} addProduct={(e, p, q) => this.addProduct(e, p, q)}/>
                </Col>
            );
        });

        let cartItems = this.state.cart.cart_items && this.state.cart.cart_items.map((item) => {
            return (
                <tr key={item.id}>
                    <td>{item.id}</td>
                    <td>{item.product.name}</td>
                    <td>{item.price_inc}</td>
                    <td>{item.quantity}</td>
                    <td>{item.quantity * item.price_inc}</td>
                    <td><Button variant={"danger"} onClick={(e) => this.removeProduct(e, item)}>Remove from
                        Cart</Button></td>
                </tr>
            );
        });

        return (
            <div className="container-fluid">
                <div className="row justify-content-center">
                    <div className="col-md-10 text-center">
                        {
                            /* This is horrid but it works */
                            this.state.loading &&
                            <Spinner animation={"border"} role={"status"}>
                                <span className={"sr-only"}>Loading...</span>
                            </Spinner>
                        }
                    </div>

                    <div className="col-md-10">
                        <Row>
                            {listItems}
                        </Row>
                    </div>

                    <div className="col-md-10"
                         style={{"borderTop": "2px solid grey", "marginTop": "8px", "paddingTop": "8px"}}>
                        <Table striped>
                            <thead>
                            <tr>
                                <th>Item ID</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Line Cost</th>
                                <th></th>
                            </tr>
                            </thead>

                            <tbody>
                            {cartItems}

                            <tr>
                                <td colSpan={"3"}></td>
                                <td><b>Total Cost:</b></td>
                                <td colSpan={1} style={{"textAlign": "right"}}>
                                    {this.state.cart.total_inc}
                                </td>
                                <td>
                                </td>
                            </tr>

                            <tr>
                                <td colSpan={6}>
                                    <Form.Group controlId="applied_coupon">
                                        <Form.Label>Apply Coupon</Form.Label>
                                        <Form.Control type="text" name={"applied_coupon"} placeholder="Apply Coupon"
                                                      onChange={(e) => this.handleChange(e)}
                                                      defaultValue={this.state.applied_coupon}/>
                                        <Button variant={"danger"} style={{"marginTop": "8px"}}
                                                onClick={(e) => this.removeCoupon(e)}>Remove Coupon</Button>
                                        <br/>
                                        <br/>
                                        <Button variant={"success"} style={{"marginTop": "8px"}}
                                                onClick={(e) => this.finaliseCart(e)}>Finalise Cart</Button>
                                    </Form.Group>
                                </td>
                            </tr>
                            </tbody>
                        </Table>
                    </div>
                </div>
            </div>
        );
    }
}

export default ProductList;

if (document.getElementById('productList')) {
    ReactDOM.render(<ProductList products={[]}/>, document.getElementById('productList'));
}

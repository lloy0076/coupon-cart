import React from 'react';
import ReactDOM from 'react-dom';
import Button from "react-bootstrap/Button";
import Modal from "react-bootstrap/Modal";

import Spinner from "react-bootstrap/Spinner";
import Alert from "react-bootstrap/Alert";
import Form from "react-bootstrap/Form";

import BootstrapTable from 'react-bootstrap-table-next';

/**
 * Handles the coupons.
 */
class Coupons extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            "coupons":        props.coupons,
            "loading":        false,
            "showAddOrEdit":  false,
            "showAddRule":    false,
            "coupon":         {
                "order": 1
            },
            "rule":           {
                "order": 1
            },
            "subject_coupon": {},
            "cook":           "",
        };
    }

    /**
     * Provides the "expand/drop down" functionality.
     */
    expandRow() {
        return {
            "renderer":           (row, b) => {
                let ruleItems = row.coupon_rules.map((item) => {
                    return (
                        <li key={item.id}>
                            <b>{item.description}</b>: {item.rule} / {item.rule_order} ({item.rule_operator}) <Button
                            variant={"outline-danger"} size={"sm"}
                            onClick={(e) => this.deleteCouponRule(e, item.id)}>-</Button></li>
                    );
                });

                let discountItems = row.discount_rules.map((item) => {
                    return (
                        <li key={item.id}>
                            <b>{item.description}</b>: {item.rule} / {item.rule_order} ({item.rule_operator}) <Button
                            variant={"outline-danger"} size={"sm"}
                            onClick={(e) => this.deleteCouponRule(e, item.id)}>-</Button></li>
                    );
                });
                return (
                    <div style={{"textAlign": "left"}}>
                        <b>Rules</b><br/>

                        <ul>
                            {ruleItems}
                            <li><Button variant={"outline-primary"} size={"sm"}
                                        onClick={(e) => this.addCouponRule(e, row, 'coupon')}>+</Button></li>
                        </ul>

                        <b>Discounts</b><br/>

                        <ul>
                            {discountItems}
                            <li><Button variant={"outline-primary"} size={"sm"}
                                        onClick={(e) => this.addCouponRule(e, row, 'discount')}>+</Button></li>
                        </ul>
                    </div>
                );
            },
            "showExpandColumn":   false,
            "expandByColumnOnly": false,
        };
    };

    /**
     * Begin a data load as soon as we are mounted.
     */
    async componentDidMount() {
        this.loadData();
    }

    /**
     * Get load from upstram.
     *
     * @returns {Promise<void>}
     */
    async loadData() {
        this.state.coupon = {};

        let uri = "https://coupon-cart.test:8443/api/coupons";

        this.startLoading();

        axios.get(uri).then((response) => {
            this.setState({"coupons": response.data});
            this.stopLoading();
        }).catch((error) => {
            console.error(error);
            this.stopLoading();
        });
    }


    /**
     * Delete the given coupon.
     *
     * @param e
     * @param id
     * @returns {Promise<void>}
     */
    async deleteCoupon(e, id) {
        this.setState({"loading": true});

        let uri = "https://coupon-cart.test:8443/api/coupons/" + encodeURIComponent(id);

        axios.delete(uri).then((response) => {
            this.stopLoading();
            this.loadData();
        }).catch((error) => {
            this.setState({"error": error.response.data.message});
            this.stopLoading();
        });

        this.closeModals();
    }

    /**
     * Delete the given coupon rules.
     *
     * @param e
     * @param id
     * @returns {Promise<void>}
     */
    async deleteCouponRule(e, id) {
        this.setState({"loading": true});

        let uri = "https://coupon-cart.test:8443/api/couponRules/" + encodeURIComponent(id);

        await axios.delete(uri).then((response) => {
            console.log(response.data);

            this.stopLoading();
            this.loadData();
        }).catch((error) => {
            console.error(error.response);
            this.setState({"error": error.response.data.message});
            this.closeModals();
            this.stopLoading();
        });

        this.stopLoading();
    }

    /**
     * Show the add coupon modal.
     *
     * @param e
     * @returns {Promise<void>}
     */
    async addCoupon(e) {
        this.setState({"coupon": {}, "showAddOrEdit": true});
    }

    /**
     * Show the add coupon rule.
     *
     * @param e
     * @param coupon
     * @param rule_type
     * @returns {Promise<void>}
     */
    async addCouponRule(e, coupon, rule_type) {
        let subjectCoupon = {
            "coupon_id":     coupon.id,
            "rule_type":     rule_type,
            "rule":          null,
            "description":   null,
            "rule_order":    1,
            "rule_not":      0,
            "rule_operator": "and",
        };

        this.setState({"subject_coupon": subjectCoupon, "showAddRule": true});
    }

    /**
     * Store the coupon.
     *
     * @param e
     * @returns {Promise<void>}
     */
    async storeCoupon(e) {
        this.startLoading();

        let uri = "https://coupon-cart.test:8443/api/coupons";

        try {
            let response = await axios.post(uri, this.state.coupon);
            this.loadData();
        } catch (error) {
            console.error(error.response);
            this.setState({"error": error.response.data.message});
            this.closeModals();
            this.stopLoading();
        }

        this.closeModals();
    }

    /**
     * Store the coupon rule.
     *
     * @param e
     * @returns {Promise<void>}
     */
    async storeCouponRule(e) {
        this.startLoading();

        let uri = "https://coupon-cart.test:8443/api/couponRules";

        try {
            let response = await axios.post(uri, this.state.subject_coupon);
            this.loadData();
        } catch (error) {
            console.error(error.response);
            this.setState({"error": error.response.data.message});
            this.stopLoading();
        }

        this.closeModals();
    }

    /**
     * Close coupon modal.
     *
     * @returns {Promise<void>}
     */
    async closeAddOrEdit() {
        this.stopLoading();
        this.setState({"coupon": {}, "showAddOrEdit": false});
    }

    /**
     * Close add rule modal.
     *
     * @returns {Promise<void>}
     */
    async closeAddRule() {
        this.stopLoading();
        this.setState({"subject_coupon": {}, "showAddRule": false});
    }

    /**
     * Close all modals.
     *
     * @returns {Promise<void>}
     */
    async closeModals() {
        this.closeAddOrEdit();
        this.closeAddRule();
    }

    /**
     * Edit the given coupon; shows the modal.
     *
     * @param e
     * @param id
     * @returns {Promise<void>}
     */
    async editCoupon(e, id) {
        this.startLoading();

        let uri = "https://coupon-cart.test:8443/api/coupons/" + encodeURIComponent(id);

        try {
            let response = await axios.get(uri);

            this.setState({"showAddOrEdit": true, "coupon": response.data});
            this.stopLoading();
        } catch (error) {
            this.setState({"error": error.response.data.message});
            this.closeModals();
            this.stopLoading();
        }
    }

    /**
     * Update the coupon.
     *
     * @param e
     * @param id
     * @returns {Promise<void>}
     */
    async updateCoupon(e, id) {
        this.startLoading();

        let uri = "https://coupon-cart.test:8443/api/coupons/" + encodeURIComponent(id);

        try {
            let objClone = {...this.state.coupon, "_method": "PUT"};

            let response = await axios.post(uri, objClone);
            this.loadData();
        } catch (error) {
            console.error(error.response);
            this.setState({"error": error.response.data.message});
            this.stopLoading();
        }

        this.closeModals();
    }

    /**
     * The form handler.
     *
     * @note We probably should do something "better" than this.
     *
     * @param e
     * @returns {Promise<void>}
     */
    async handleChange(e) {
        let coupon         = this.state.coupon;
        let subject_coupon = this.state.subject_coupon;

        switch (e.target.name) {
            case "display_name": {
                coupon.display_name = e.target.value;
                break;
            }

            case "coupon_code": {
                coupon.coupon_code = e.target.value;
                break;
            }

            case "order": {
                coupon.order = e.target.value;
                break;
            }

            case "rule_coupon_id": {
                subject_coupon.coupon_id = e.target.value;
                break;
            }

            case "rule_type": {
                subject_coupon.rule_type = e.target.value;
                break;
            }

            case "rule": {
                subject_coupon.rule = e.target.value;
                break;
            }

            case "rule_description": {
                subject_coupon.rule_description = e.target.value;
                break;
            }

            case "rule_order": {
                subject_coupon.rule_order = e.target.value;
                break;
            }

            case "rule_not": {
                console.log(event.target.name);
                subject_coupon.rule_not = e.target.checked ? 1 : 0;
                break;
            }

            case "rule_operator": {
                subject_coupon.rule_operator = e.target.value;
                break;
            }

            default:
                console.log(`Target ${e.target.name} not handled yet`);
        }

        this.setState({"coupon": coupon});
    }

    /**
     * Perform basic loading tidy ups.
     */
    startLoading() {
        this.setState({"loading": true});
        this.clearErrors();
    }

    /**
     *
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
     * Display the actual data.
     */
    render() {
        const workingCoupons = this.state.coupon.id === undefined ? this.state.coupons : [this.state.coupon];

        const columns = ([
            {
                "dataField": "id",
                "text":      "ID",
            },
            {
                "dataField": "display_name",
                "text":      "Display Name",
            },
            {
                "dataField": "coupon_code",
                "text":      "Coupon Code",
            },
            {
                "dataField":    "delete",
                "isDummyField": true,
                "text":         "Delete",
                "formatter":    (cellContent, row) => {
                    return (
                        <div>
                            <Button variant={"danger"}
                                    onClick={(e) => this.deleteCoupon(e, row.id)}>Delete</Button>&nbsp;
                            <Button variant={"success"} onClick={(e) => this.editCoupon(e, row.id)}>Edit</Button>
                        </div>
                    );
                }
            }
        ]);

        /**
         * This is the actual render!
         */
        return (
            <div className="container-fluid">
                {/* @TODO These modals should be in some separate component. */}
                <Modal show={this.state.showAddOrEdit} onHide={(e) => this.closeAddOrEdit(e)} animation={true}>
                    <Modal.Header>
                        <Modal.Title>{this.state.coupon.id === undefined ? 'Add' : 'Edit'} Coupon</Modal.Title>
                    </Modal.Header>

                    <Modal.Body>
                        <Form>
                            <Form.Group controlId="display_name">
                                <Form.Label>Display Name</Form.Label>
                                <Form.Control type="text" name={"display_name"} placeholder="Display Name"
                                              onChange={(e) => this.handleChange(e)}
                                              defaultValue={this.state.coupon.display_name}/>
                            </Form.Group>

                            <Form.Group controlId="coupon_code">
                                <Form.Label>Coupon Code</Form.Label>
                                <Form.Control type="text" name={"coupon_code"} placeholder="Coupon Code"
                                              onChange={(e) => this.handleChange(e)}
                                              defaultValue={this.state.coupon.coupon_code}/>
                            </Form.Group>

                            <Form.Group controlId="order">
                                <Form.Label>Order</Form.Label>
                                <Form.Control type="number" name={"order"} placeholder="1"
                                              onChange={(e) => this.handleChange(e)}
                                              defaultValue={this.state.coupon.order}/>
                            </Form.Group>
                        </Form>
                    </Modal.Body>

                    <Modal.Footer>
                        <Button variant="secondary" onClick={(e) => this.closeAddOrEdit(e)}>Cancel</Button>
                        <Button variant="primary" onClick={(e) => this.state.coupon.id === undefined ?
                                                                  this.storeCoupon(e) :
                                                                  this.updateCoupon(e,
                                                                      this.state.coupon.id)}>Ok</Button>
                    </Modal.Footer>
                </Modal>

                {/* @TODO These modals should be in some separate component. */}
                <Modal show={this.state.showAddRule} onHide={(e) => this.closeAddRule(e)} animation={true}>
                    <Modal.Header>
                        {/*<Modal.Title>Add Rule <Button onClick={(e) => console.info(this.state)}>C</Button></Modal.Title>*/}
                        <Modal.Title>Add Rule</Modal.Title>
                    </Modal.Header>

                    <Modal.Body>
                        <Form>
                            <Form.Group controlId="rule_coupon_id">
                                <Form.Label>Coupon Id</Form.Label>
                                <Form.Control type="text" name={"rule_coupon_id"} placeholder=""
                                              defaultValue={this.state.subject_coupon.id} disabled={true}/>
                            </Form.Group>

                            <Form.Group controlId="rule_type">
                                <Form.Label>Rule Type</Form.Label>
                                <Form.Control type="text" name={"rule_type"} placeholder="Rule Type"
                                              onChange={(e) => this.handleChange(e)}
                                              defaultValue={this.state.subject_coupon.rule_type}/>
                            </Form.Group>

                            <Form.Group controlId="rule">
                                <Form.Label>Rule</Form.Label>
                                <Form.Control type="text" name={"rule"} placeholder="Rule"
                                              onChange={(e) => this.handleChange(e)}
                                              defaultValue={this.state.subject_coupon.rule}/>
                            </Form.Group>

                            <Form.Group controlId="rule_description">
                                <Form.Label>Rule Description</Form.Label>
                                <Form.Control type="text" name={"rule_description"} placeholder="Rule Description"
                                              onChange={(e) => this.handleChange(e)}
                                              defaultValue={this.state.subject_coupon.rule_description}/>
                            </Form.Group>

                            <Form.Group controlId="rule_order">
                                <Form.Label>Rule Order</Form.Label>
                                <Form.Control type="number" name={"rule_order"} placeholder="1"
                                              onChange={(e) => this.handleChange(e)}
                                              defaultValue={this.state.subject_coupon.rule_order}/>
                            </Form.Group>

                            <Form.Group controlId="rule_not">
                                <Form.Label>Rule Not</Form.Label>
                                <Form.Check type={"checkbox"} name={"rule_not"} onChange={(e) => this.handleChange(e)}/>
                            </Form.Group>

                            <Form.Group controlId="rule_operator">
                                <Form.Label>Rule Operator</Form.Label>
                                <Form.Control type="text" name={"rule_operator"} placeholder="1"
                                              onChange={(e) => this.handleChange(e)}
                                              defaultValue={this.state.subject_coupon.rule_operator}/>
                            </Form.Group>
                        </Form>
                    </Modal.Body>

                    <Modal.Footer>
                        <Button variant="secondary" onClick={(e) => this.closeAddRule(e)}>Cancel</Button>
                        <Button variant="primary" onClick={(e) => this.storeCouponRule(e)}>Ok</Button>
                    </Modal.Footer>
                </Modal>

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

                    {
                        // This isn't pretty but it's sufficient for a demo.
                        this.state.error &&
                        <div className="col-md-10 text-center">
                            <Alert variant={"danger"}>
                                <Alert.Heading>Error</Alert.Heading>

                                {this.state.error}
                            </Alert>
                        </div>
                    }

                    <div className="col-md-10" style={{"marginBottom": "16px", "textAlign": "right"}}>
                        <Button variant={"primary"} onClick={(e) => this.addCoupon(e)}>Add</Button>
                    </div>
                </div>

                <div className={"row justify-content-center"}>
                    <div className="col-md-10 text-center">
                        {/*The actual table that does the work - finally!*/}
                        <BootstrapTable bootstrap4 striped keyField={"id"} data={workingCoupons}
                                        columns={columns} expandRow={this.expandRow()}></BootstrapTable>
                    </div>
                </div>
            </div>
        );
    }

}

export default Coupons;

if (document.getElementById('coupons')) {
    ReactDOM.render(<Coupons coupons={[]}/>, document.getElementById('coupons'));
}

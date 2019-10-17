import React from 'react';
import Card from "react-bootstrap/Card";
import Button from "react-bootstrap/Button";

class ProductCard extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            "addProduct": props.addProduct,
            "removeProduct": props.removeProduct,
        };
    }

    render() {
        return (
            <Card key={this.props.product.id} style={{"width": "320px", "textAlign": "center"}}>
                <Card.Img variant={"top"} src={this.props.product.image} style={{"width": "320px", "height": "100%"}}/>
                <Card.Body>
                    <Card.Title>{this.props.product.name} | $&nbsp;{this.props.product.price}</Card.Title>
                    <Card.Text>{this.props.product.description} | <b>$&nbsp;{this.props.product.price}</b></Card.Text>
                    <Button variant={"primary"} onClick={(e) => this.state.addProduct(e, this.props.product, 1)}>Add to Cart</Button>
                </Card.Body>
            </Card>
        );
    }
}

export default ProductCard;

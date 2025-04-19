import React, { Component } from "react";
import { createRoot } from "react-dom";
import axios from "axios";
import Swal from "sweetalert2";
import { sum } from "lodash";
import { PDFViewer } from '@react-pdf/renderer'; // Import PDFViewer
import Invoice from "./invoice";

class Cart extends Component {
    constructor(props) {
        super(props);
        this.state = {
            cart: [],
            products: [],
            customers: [],
            barcode: "",
            search: "",
            customer_id: "",
            translations: {},
            invoiceData: null, // State to hold invoice data
            showInvoice: false, // State to control invoice visibility
        };
        this.loadCart = this.loadCart.bind(this);
        this.handleOnChangeBarcode = this.handleOnChangeBarcode.bind(this);
        this.handleScanBarcode = this.handleScanBarcode.bind(this);
        this.handleChangeQty = this.handleChangeQty.bind(this);
        this.handleEmptyCart = this.handleEmptyCart.bind(this);
        this.loadProducts = this.loadProducts.bind(this);
        this.handleChangeSearch = this.handleChangeSearch.bind(this);
        this.handleSeach = this.handleSeach.bind(this);
        this.setCustomerId = this.setCustomerId.bind(this);
        this.handleClickSubmit = this.handleClickSubmit.bind(this);
        this.loadTranslations = this.loadTranslations.bind(this);
    }

    componentDidMount() {
        this.loadTranslations();
        this.loadCart();
        this.loadProducts();
        this.loadCustomers();
        document.body.addEventListener("darkModeToggle", this.forceUpdate.bind(this));
    }

    componentWillUnmount() {
        document.body.removeEventListener("darkModeToggle", this.forceUpdate.bind(this));
    }

    loadTranslations() {
        axios.get("/admin/locale/cart")
            .then((res) => {
                const translations = res.data;
                this.setState({ translations });
            })
            .catch((error) => {
                console.error("Error loading translations:", error);
            });
    }

    loadCustomers() {
        axios.get(`/admin/customers`).then((res) => {
            const customers = res.data;
            this.setState({ customers });
        });
    }

    loadProducts(search = "") {
        const query = !!search ? `?search=${search}` : "";
        axios.get(`/admin/products${query}`).then((res) => {
            const products = res.data.data;
            this.setState({ products });
        });
    }

    handleOnChangeBarcode(event) {
        const barcode = event.target.value;
        this.setState({ barcode });
    }

    loadCart() {
        axios.get("/admin/cart").then((res) => {
            const cart = res.data;
            this.setState({ cart });
        });
    }

    handleScanBarcode(event) {
        event.preventDefault();
        const { barcode } = this.state;
        if (!!barcode) {
            axios.post("/admin/cart", { barcode })
                .then((res) => {
                    this.loadCart();
                    this.setState({ barcode: "" });
                })
                .catch((err) => {
                    Swal.fire("Error!", err.response.data.message, "error");
                });
        }
    }

    handleChangeQty(product_id, qty) {
        const cart = this.state.cart.map((c) => {
            if (c.id === product_id) {
                c.pivot.quantity = qty;
            }
            return c;
        });
        this.setState({ cart });
        if (!qty) return;
        axios.post("/admin/cart/change-qty", { product_id, quantity: qty })
            .then((res) => {})
            .catch((err) => {
                Swal.fire("Error!", err.response.data.message, "error");
            });
    }

    getTotal(cart) {
        const total = cart.map((c) => c.pivot.quantity * c.price);
        return sum(total).toFixed(2);
    }

    handleClickDelete(product_id) {
        axios.post("/admin/cart/delete", { product_id, _method: " DELETE" })
            .then((res) => {
                const cart = this.state.cart.filter((c) => c.id !== product_id);
                this.setState({ cart });
            });
    }

    handleEmptyCart() {
        axios.post("/admin/cart/empty", { _method: "DELETE" }).then((res) => {
            this.setState({ cart: [] });
        });
    }

    handleChangeSearch(event) {
        const search = event.target.value;
        this.setState({ search });
    }

    handleSeach(event) {
        if (event.keyCode === 13) {
            this.loadProducts(event.target.value);
        }
    }

    addProductToCart(barcode) {
        let product = this.state.products.find((p) => p.barcode === barcode);
        if (!!product) {
            let cart = this.state.cart.find((c) => c.id === product.id);
            if (!!cart) {
                this.setState({
                    cart: this.state.cart.map((c) => {
                        if (c.id === product.id && product.quantity > c.pivot.quantity) {
                            c.pivot.quantity += 1;
                        }
                        return c;
                    }),
                });
            } else {
                if (product.quantity > 0) {
                    product = {
                        ...product,
                        pivot: {
                            quantity: 1,
                            product_id: product.id,
                            user_id: 1,
                        },
                    };
                    this.setState({ cart: [...this.state.cart, product] });
                }
            }
            axios.post("/admin/cart", { barcode })
                .then((res) => {})
                .catch((err) => {
                    Swal.fire("Error!", err.response.data.message, "error");
                });
        }
    }

    setCustomerId(event) {
        this.setState({ customer_id: event.target.value });
    }

    handleClose = () => {
        this.setState({ showInvoice: false });
    };

    handleClickSubmit() {
        Swal.fire({
            title: this.state.translations["received_amount"],
            input: "text",
            inputValue: this.getTotal(this.state.cart),
            cancelButtonText: this.state.translations["cancel_pay"],
            showCancelButton: true,
            confirmButtonText: this.state.translations["confirm_pay"],
            showLoaderOnConfirm: true,
            preConfirm: (amount) => {
                const totalAmount = this.getTotal(this.state.cart);
                const receivedAmount = parseFloat(amount);
                const balance = receivedAmount - totalAmount;

                return axios.post("/admin/orders", {
                    customer_id: this.state.customer_id,
                    amount: totalAmount,
                })
                .then((res) => {
                    const { order, order_id } = res.data;

                    this.loadCart();

                    this.setState({
                        invoiceData: {
                            order_id,
                            customer: order.customer,
                            amount: totalAmount,
                            receivedAmount,
                            balance,
                            cart: this.state.cart,
                        },
                    });

                    // Show confirmation dialog
                    return Swal.fire({
                        title: this.state.translations["invoice_ready"],
                        text: this.state.translations["view_invoice"],
                        icon: "success",
                        confirmButtonText: this.state.translations["confirm"],
                        cancelButtonText: this.state.translations["cancel"],
                        showCancelButton: true,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.setState({ showInvoice: true }); // Show the invoice after confirmation
                        }
                        {/* PDF Viewer for Invoice */}
                    });
                })
                .catch((err) => {
                    Swal.showValidationMessage(err.response.data.message);
                });
            },
            allowOutsideClick: () => !Swal.isLoading(),
        });
    }

    render() {
        const { cart, products, customers, barcode, translations, showInvoice, invoiceData } = this.state;

        const isDarkMode = document.body.classList.contains("dark-mode");

        const containerStyle = {
            display: "flex",
            height: "100vh",
            padding: "20px",
            backgroundColor: isDarkMode ? "#121212" : "#f8f9fa",
            color: isDarkMode ? "#ffffff" : "#000000",
            fontFamily: "Arial, sans-serif",
        };

        const sectionStyle = {
            flex: 1,
            marginRight: "20px",
            padding: "20px",
            backgroundColor: isDarkMode ? "#1e1e1e" : "#ffffff",
            borderRadius: "10px",
            boxShadow: isDarkMode ? "0 4px 6px rgba(255, 255, 255, 0.1)" : "0 4px 6px rgba(0, 0, 0, 0.1)",
        };

        const inputStyle = {
            width: "100%",
            padding: "10px",
            marginBottom: "15px",
            border: isDarkMode ? "1px solid #444" : "1px solid #ccc",
            borderRadius: "5px",
            fontSize: "14px",
            backgroundColor: isDarkMode ? "#333" : "#ffffff",
            color: isDarkMode ? "#ffffff" : "#000000",
        };

        const buttonStyle = {
            padding: "10px",
            border: "none",
            borderRadius: "5px",
            cursor: "pointer",
            fontSize: "14px",
        };

        const tableHeaderStyle = {
            borderBottom: isDarkMode ? "1px solid #444" : "1px solid #ddd",
            backgroundColor: isDarkMode ? "#333" : "#f1f1f1",
            color: isDarkMode ? "#ffffff" : "#000000",
        };

        return (
            <>
                {showInvoice && invoiceData && (
                    <div style={{
                        position: 'fixed',
                        top: '50%',
                        left: '50%',
                        transform: 'translate(-50%, -50%)',
                        zIndex: 1000,
                        marginTop: '60px',
                        marginLeft: '110px',
                        height: '100%',
                        width: '85%',
                        backgroundColor: 'white',
                        boxShadow: '0 4px 8px rgba(0, 0, 0, 0.2)',
                    }}>
                        <button onClick={this.handleClose} style={{
                        position: 'absolute',
                        top: '50px',
                        right: '10px',
                        backgroundColor: 'red',
                        color: 'white',
                        border: 'none',
                        borderRadius: '5px',
                        padding: '5px 10px',
                        cursor: 'pointer',
                        }}>
                        Close
                        </button>
                        <PDFViewer style={{ width: '100%', height: '100%' }}>
                            <Invoice orderData={invoiceData} />
                        </PDFViewer>
                    </div>
                )}
                <div style={containerStyle}>
                    {/* Left Section */}
                    <div style={sectionStyle}>
                        {/* Barcode Scanner */}
                        <form onSubmit={this.handleScanBarcode}>
                            <input
                                type="text"
                                style={inputStyle}
                                placeholder={translations["scan_barcode"]}
                                value={barcode}
                                onChange={this.handleOnChangeBarcode}
                            />
                        </form>

                        {/* Customer Dropdown */}
                        <select
                            style={inputStyle}
                            onChange={this.setCustomerId}
                        >
                            <option value="">
                                {translations["general_customer"]}
                            </option>
                            {customers.map((cus) => (
                                <option key={cus.id} value={cus.id}>
                                    {`${cus.first_name} ${cus.last_name}`}
                                </option>
                            ))}
                        </select>

                        {/* Cart Table */}
                        <div
                            style={{
                                maxHeight: "400px",
                                overflowY: "auto",
                                marginBottom: "15px",
                                border: isDarkMode ? "1px solid #444" : "1px solid #ddd",
                                borderRadius: "5px",
                            }}
                        >
                            <table
                                style={{
                                    width: "100%",
                                    borderCollapse: "collapse",
                                }}
                            >
                                <thead style={tableHeaderStyle}>
                                    <tr>
                                        <th style={{ textAlign: "left", padding: "8px", fontSize: "14px" }}>
                                            {translations["product_name"]}
                                        </th>
                                        <th style={{ textAlign: "center", padding: "8px", fontSize: "14px" }}>
                                            {translations["quantity"]}
                                        </th>
                                        <th style={{ textAlign: "right", padding: "8px", fontSize: "14px" }}>
                                            {translations["price"]}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {cart.map((c) => (
                                        <tr key={c.id}>
                                            <td style={{ textAlign: "left", padding: "8px", fontSize: "14px" }}>
                                                {c.name}
                                            </td>
                                            <td style={{ textAlign: "center", padding: "8px" }}>
                                                <input
                                                    type="text"
                                                    style={{ ...inputStyle, width: "60px" }}
                                                    value={c.pivot.quantity}
                                                    onChange={(event) => this.handleChangeQty(c.id, event.target.value)}
                                                />
                                                <button
                                                    style={{
                                                        marginLeft: "5px",
                                                        padding: "5px 10px",
                                                        backgroundColor: "#dc3545",
                                                        color: "#fff",
                                                        border: "none",
                                                        borderRadius: "5px",
                                                        cursor: "pointer",
                                                        fontSize: "14px",
                                                    }}
                                                    onClick={() => this.handleClickDelete(c.id)}
                                                >
                                                    <i className="fas fa-trash"></i>
                                                </button>
                                            </td>
                                            <td style={{ textAlign: "right", padding: "8px", fontSize: "14px" }}>
                                                {window.APP.currency_symbol} {(c.price * c.pivot.quantity).toFixed(2)}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>

                        {/* Total and Buttons */}
                        <div style={{ display: "flex", justifyContent: "space-between", marginBottom: "15px" }}>
                            <div style={{ fontSize: "16px", fontWeight: "bold" }}>
                                {translations["total"]}:
                            </div>
                            <div style={{ fontSize: "16px", fontWeight: "bold" }}>
                                {window.APP.currency_symbol} {this.getTotal(cart)}
                            </div>
                        </div>
                        <div style={{ display: "flex", justifyContent: "space-between" }}>
                            <button
                                style={{
                                    ...buttonStyle,
                                    flex: 1,
                                    backgroundColor: "#dc3545",
                                    color: "#fff",
                                    marginRight: "10px",
                                }}
                                onClick={this.handleEmptyCart}
                                disabled={!cart.length}
                            >
                                {translations["cancel"]}
                            </button>
                            <button
                                style={{
                                    ...buttonStyle,
                                    flex: 1,
                                    backgroundColor: "#007bff",
                                    color: "#fff",
                                }}
                                disabled={!cart.length}
                                onClick={this.handleClickSubmit}
                            >
                                {translations["checkout"]}
                            </button>
                        </div>
                    </div>

                    {/* Right Section */}
                    <div style={sectionStyle}>
                        {/* Search Bar */}
                        <input
                            type="text"
                            style={inputStyle}
                            placeholder={translations["search_product"] + "..."}
                            onChange={this.handleChangeSearch}
                            onKeyDown={this.handleSeach}
                        />

                        {/* Product Grid */}
                        <div
                            style={{
                                display: "grid",
                                gridTemplateColumns: "repeat(auto-fill, minmax(150px, 1fr))",
                                gap: "20px",
                            }}
                        >
                            {products.map((p) => (
                                <div
                                    key={p.id}
                                    onClick={() => this.addProductToCart(p.barcode)}
                                    style={{
                                        border: isDarkMode ? "1px solid #444" : "1px solid #ddd",
                                        borderRadius: "10px",
                                        padding: "10px",
                                        textAlign: "center",
                                        cursor: "pointer",
                                        transition: "transform 0.2s",
                                        backgroundColor: isDarkMode ? "#333" : "#ffffff",
                                        color: isDarkMode ? "#ffffff" : "#000000",
                                    }}
                                    onMouseEnter={(e) =>
                                        (e.currentTarget.style.transform = "scale(1.05)")
                                    }
                                    onMouseLeave={(e) =>
                                        (e.currentTarget.style.transform = "scale(1)")
                                    }
                                >
                                    <img
                                        src={p.image_url}
                                        alt={p.name}
                                        style={{
                                            width: "100%",
                                            height: "100px",
                                            objectFit: "cover",
                                            borderRadius: "5px",
                                        }}
                                    />
                                    <h5
                                        style={{
                                            marginTop: "10px",
                                            fontSize: "14px",
                                            color:
                                                window.APP.warning_quantity > p.quantity
                                                    ? "red"
                                                    : isDarkMode
                                                    ? "#ffffff"
                                                    : "#000000",
                                        }}
                                    >
                                        {p.name} ({p.quantity})
                                    </h5>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </>
        );
    }
}

export default Cart;

const root = document.getElementById("cart");
if (root) {
    const rootInstance = createRoot(root);
    rootInstance.render(<Cart />);
}

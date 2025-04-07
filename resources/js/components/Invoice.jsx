// Invoice.js
import React from 'react';
import { Page, Text, View, Document, StyleSheet } from '@react-pdf/renderer';

const styles = StyleSheet.create({
    page: {
        flexDirection: 'column',
        padding: 30,
    },
    section: {
        marginBottom: 10,
    },
    header: {
        fontSize: 20,
        textAlign: 'center',
    },
    table: {
        display: 'table',
        width: 'auto',
        margin: 'auto',
    },
    tableRow: {
        flexDirection: 'row',
    },
    tableCol: {
        width: '25%',
        border: '1px solid #000',
        padding: 5,
    },
    total: {
        fontSize: 16,
        fontWeight: 'bold',
    },
});

const Invoice = ({ orderData }) => (
    <Document>
        <Page size="A4" style={styles.page}>
            <View style={styles.section}>
                <Text style={styles.header}>Crow.lk</Text>
                <Text style={styles.header}>INVOICE</Text>
                <Text>Customer Name: {orderData.customer_id}</Text>
                <Text>Date: {new Date().toLocaleDateString()}</Text>
            </View>
            <View style={styles.section}>
                <Text>Items</Text>
                <View style={styles.table}>
                    <View style={styles.tableRow}>
                        <Text style={styles.tableCol}>Name</Text>
                        <Text style={styles.tableCol}>Quantity</Text>
                        <Text style={styles.tableCol}>Price (Rs.)</Text>
                    </View>
                    {orderData.cart.map((item) => (
                        <View style={styles.tableRow} key={item.id}>
                            <Text style={styles.tableCol}>{item.name}</Text>
                            <Text style={styles.tableCol}>{item.pivot.quantity}</Text>
                            <Text style={styles.tableCol}>{item.price}</Text>
                        </View>
                    ))}
                </View>
                <Text style={styles.total}>Total Amount: {orderData.amount}</Text>
            </View>
        </Page>
    </Document>
);

export default Invoice;

function validateForm() {
    let isValid = true;

    const productName = document.getElementById('productname').value;
    const brand = document.getElementById('brand').value;
    const type = document.getElementById('type').value;
    const sku = document.getElementById('sku').value;
    const dateAdded = document.getElementById('dateadded').value;

    const skupattern = /^[0-9]+$/;
    const current_date = new Date();
    const selected_date = new Date(dateAdded);

    document.getElementById('productNameError').innerText = '';
    document.getElementById('brandError').innerText = '';
    document.getElementById('typeError').innerText = '';
    document.getElementById('skuError').innerText = '';
    document.getElementById('dateAddedError').innerText = '';

    if (productName === '') {
        document.getElementById('productNameError').innerText = 'Product Name is required';
        isValid = false;
    }

    if (brand === '') {
        document.getElementById('brandError').innerText = 'Brand is required';
        isValid = false;
    }

    if (type === '') {
        document.getElementById('typeError').innerText = 'Type is required';
        isValid = false;
    }

    if (sku === '') {
        document.getElementById('skuError').innerText = 'SKU is required';
        isValid = false;
    }else if(!skupattern.test(sku)){
        document.getElementById('skuError').innerText = 'Entered sku is not valid'
    }

    if (dateAdded === '') {
        document.getElementById('dateAddedError').innerText = 'Date is required';
        isValid = false;
    }else if(selected_date>current_date) {
        document.getElementById('dateAddedError').innerText='Entered Date is not valid';
        isValid = false;
    }

    return isValid;
}



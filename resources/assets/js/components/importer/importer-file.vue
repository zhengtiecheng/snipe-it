<style>
tr {
    padding-left:30px;
}
</style>

<template>
    <tr v-show="processDetail">
        <td colspan="3">
            <h4 class="modal-title">Import File:</h4>
            <div class="dynamic-form-row">
                <div class="col-md-4 col-xs-12">
                    <label for="import-type">Import Type:</label>
                </div>
                <div class="col-md-4 col-xs-12">
                    <select2 :options="options.importTypes" v-model="options.importType">
                        <option disabled value="0"></option>
                    </select2>
                </div>
            </div>
            <div class="dynamic-form-row">
                <div class="col-md-4 col-xs-12">
                    <label for="import-update">Update Existing Values?:</label>
                </div>
                <div class="col-md-4 col-xs-12">
                    <input type="checkbox" name="import-update" v-model="options.update">
                </div>
            </div>

            <div class="col-md-12" style="padding-top: 30px;">
            <table class="table">
            <thead>
                <th>Header Field</th>
                <th>Import Field</th>
                <th>Sample Value</th>
            </thead>
            <tbody>
            <template v-for="(header, index) in file.header_row">
                <tr>
                    <td>
                    <label :for="header" class="controllabel">{{ header }}</label>
                    </td>
                    <td>
                        <div required>
                            <select2 :options="columns" v-model="columnMappings[header]">
                                <option value="0">Do Not Import</option>
                            </select2>
                        </div>
                    </td>
                    <td>
                        <div>{{ activeFile.first_row[index] }}</div>
                    </td>
                </tr>
                </template>
            </tbody>
            </table>
            </div>
        </td>

        <td>
            <button type="button" class="btn btn-default" @click="processDetail = false">Cancel</button>
            <button type="submit" class="btn btn-primary" @click="postSave">Import</button>
            <div class="alert alert-success col-md-5 col-md-offset-1" style="text-align:left" v-if="statusText">{{ this.statusText }}</div>
        </td>
    </tr>
</template>

<script>
    export default {
        props: ['file'],
        data() {
            return {
                activeFile: this.file,
                processDetail: false,
                statusText: null,
                options: {
                    importType: this.file.import_type,
                    update: false,
                    importTypes: [
                        { id: 'asset', text: 'Assets' },
                        { id: 'accessory', text: 'Accessories' },
                        { id: 'consumable', text: 'Consumables' },
                        { id: 'component', text: 'Components' },
                        { id: 'license', text: 'Licenses' },
                        { id: 'user', text: 'Users' }
                    ],
                    statusText: null,
                },
                columnOptions: {
                    general: [
                        {id: 'category', text: 'Category' },
                        {id: 'company', text: 'Company' },
                        {id: 'checkout_to', text: 'Checked out to' },
                        {id: 'email', text: 'Email' },
                        {id: 'first_name', text: 'First Name' },
                        {id: 'item_name', text: 'Item Name' },
                        {id: 'last_name', text: 'Last Name' },
                        {id: 'location', text: 'Location' },
                        {id: 'maintained', text: 'Maintained' },
                        {id: 'manufacturer', text: 'Manufacturer' },
                        {id: 'notes', text: 'Notes' },
                        {id: 'order_number', text: 'Order Number' },
                        {id: 'purchase_cost', text: 'Purchase Cost' },
                        {id: 'purchase_date', text: 'Purchase Date' },
                        {id: 'quantity', text: 'Quantity' },
                        {id: 'requestable', text: 'Requestable' },
                        {id: 'serial', text: 'Serial Number' },
                        {id: 'supplier', text: 'Supplier' },
                        {id: 'username', text: 'Username' },
                    ],
                    assets: [
                        {id: 'asset_tag', text: 'Asset Tag' },
                        {id: 'asset_model', text: 'Model Name' },
                        {id: 'image', text: 'Image Filename' },
                        {id: 'model_number', text: 'Model Number' },
                        {id: 'name', text: 'Full Name' },
                        {id: 'status', text: 'Status' },
                        {id: 'warranty_months', text: 'Warranty Months' },
                    ],
                    licenses: [
                        {id: 'expiration_date', text: 'Expiration Date' },
                        {id: 'license_email', text: 'Licensed To Email' },
                        {id: 'license_name', text: 'Licensed To Name' },
                        {id: 'purchase_order', text: 'Purchase Order' },
                        {id: 'reassignable', text: 'Reassignable' },
                        {id: 'seats', text: 'Seats' },
                    ],
                    users: [
                        {id: 'employee_num', text: 'Employee Number' },
                        {id: 'jobtitle', text: 'Job Title' },
                        {id: 'phone_number', text: 'Phone Number' },
                    ],
                    customFields: [],
                },
                columnMappings: this.file.field_map || {},
                activeColumn: null,
            }
        },
        created() {
            this.fetchCustomFields();
            window.eventHub.$on('showDetails', this.toggleExtendedDisplay)
            this.populateSelect2ActiveItems();
        },
        computed: {
            columns() {
                switch(this.options.importType) {
                    case 'asset':
                        return this.columnOptions.general.concat(this.columnOptions.assets).concat(this.columnOptions.customFields);
                    case 'license':
                        return this.columnOptions.general.concat(this.columnOptions.licenses);
                    case 'user':
                        return this.columnOptions.general.concat(this.columnOptions.users);
                }
                return this.columnOptions.general;
            }
        },
        methods: {
            fetchCustomFields() {
                this.$http.get('/api/v1/fields')
                .then( ({data}) => {
                    data = data.rows;
                    data.forEach((item) => {
                        this.columnOptions.customFields.push({
                            'id': item.db_column_name,
                            'text': item.name,
                        })
                    })
                });
            },
            postSave() {
                this.statusText = "Processing...";
                this.$http.post('/api/v1/imports/process/'+this.file.id, {
                    'import-update': this.options.update,
                    'import-type': this.options.importType,
                    'column-mappings': this.columnMappings
                }).then( (response) => {
                    // Success
                    this.statusText = "Success... Redirecting.";
                    window.location.href = response.body.messages.redirect_url;
                }, (response) => {
                    // Failure
                    if(response.body.status == 'import-errors') {
                        window.eventHub.$emit('importErrors', response.body.messages);
                        this.statusText = "Error";
                    } else {
                        this.$emit('alert', {
                            message: response.body.messages,
                            type: "danger",
                            visible: true,
                        })
                    }
                    this.displayImportModal=false;
                });
            },
            populateSelect2ActiveItems() {
                if(this.file.field_map == null) {
                    // Begin by populating the active selection in dropdowns with blank values.
                    for (var i=0; i < this.file.header_row.length; i++) {
                        this.$set(this.columnMappings, this.file.header_row[i], null);
                    }
                    // Then, for any values that have a likely match, we make that active.
                    for(var j=0; j < this.columns.length; j++) {
                        let column = this.columns[j];
                        let index = this.file.header_row.indexOf(column.text)
                        if(index != -1) {
                            this.$set(this.columnMappings, this.file.header_row[index], column.id)
                        }
                    }
                }
            },
            toggleExtendedDisplay(fileId) {
                if(fileId == this.file.id) {
                    this.processDetail = !this.processDetail
                }
            },
            updateModel(header, value) {
                console.log(header, value);
                this.columnMappings[header] = value;
            }
        },
        components: {
            select2: require('../select2.vue')
        }
    }
</script>

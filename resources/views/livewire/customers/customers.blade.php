<div>
    <livewire:components.breadcrum :title="$title"/>
    <livewire:components.table-component
        :modelNamespace="$model"
        :columns="['name' ,'email', 'phone_number', 'credit_limit', 'credit_days']"
        :search="$search"
        :itemsPerPage="$itemsPerPage"
        searchPlaceHolder='Search item E.g Panadol or Bandage'
        viewRoute='customer.show'
    />
</div>
<div>
    <livewire:components.breadcrum :title="$title"/>
    <livewire:components.table-component
        :modelNamespace="$model"
        :columns="['id','user.name', 'customer.name', 'total_amount', 'status', 'created_at']"
        :search="$search"
        :itemsPerPage="$itemsPerPage"
        searchPlaceHolder='Search sale '
        viewRoute='sale.show'
    />
</div>

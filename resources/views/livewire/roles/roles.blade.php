<div>
    <livewire:components.breadcrum :title="$title"/>
    <livewire:components.table-component
        :modelNamespace="$model"
        :columns="['name', 'created_at']"
        :search="$search"
        :itemsPerPage="$itemsPerPage"
        searchPlaceHolder='Search role E.g Admin..'
        viewRoute='role.show'
    />
</div>

<div>
    <livewire:components.breadcrum :title="$title"/>
    <livewire:components.table-component
        :modelNamespace="$model"
        :columns="['name', 'created_at']"
        :search="$search"
        :itemsPerPage="$itemsPerPage"
        searchPlaceHolder='Search Item type E.g .. Pain killer'
        viewRoute='itemType.show'
    />
</div>

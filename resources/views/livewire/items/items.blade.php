<div>
    <livewire:components.breadcrum :title="$title"/>
    <livewire:components.table-component
        :modelNamespace="$model"
        :columns="['name' ,'unit_price', 'manufacturer', 'description', 'created_at']"
        :search="$search"
        :itemsPerPage="$itemsPerPage"
        searchPlaceHolder='Search item E.g Panadol or Bandage'
        viewRoute='item.show'
    />
</div>


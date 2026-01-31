<div>
    <livewire:components.breadcrum :title="$title"/>
    <livewire:components.table-component
        :modelNamespace="$model"
        :columns="['name', 'email', 'branch.name','created_at']"
        :search="$search"
        :itemsPerPage="$itemsPerPage"
        searchPlaceHolder='Search user E.g .. John doe'
        viewRoute='user.show'
    />
</div>

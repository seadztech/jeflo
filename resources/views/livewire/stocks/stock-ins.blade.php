<div>
    <livewire:components.breadcrum :title="$title"/>
    <livewire:components.table-component
        :modelNamespace="$model"
        :columns="['type', 'transaction_code', 'amount', 'response',  'created_at']"
        :search="$search"
        :itemsPerPage="$itemsPerPage"
        searchPlaceHolder='Search  E.g TXHRUSKA'
        viewRoute='transaction.show'
    />
</div>

<?php

namespace App\Livewire\Components;

use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;
use phpDocumentor\Reflection\Types\Boolean;

class TableComponent extends Component
{
    use WithPagination;

    public $modelNamespace;
    public $columns = [];
    public $searchableColumns = [];
    public $viewRoute;
    public $entityName;
    public $search;
    public $itemsPerPage;
    public $searchPlaceHolder = 'Search...';
    public $showTable = true;
    public $showCreateButton;
    public $itemToShow = [];
    public $itemToCompare = [];
    public $whereClauses = [];

    protected $paginationTheme = 'tailwind';
    protected $listeners = ['showTable' => 'showTableView'];

    public function mount($modelNamespace = null, $columns = [], $viewRoute = '', $searchableColumns = [], $search = '', $itemsPerPage = 10, $showCreateButton = true, $whereClauses = [])
    {
        if (!$modelNamespace) {
            throw new \Exception('Model namespace not provided.');
        }

        if (!class_exists($modelNamespace)) {
            throw new \Exception("Model does not exist - $modelNamespace");
        }

        $this->modelNamespace = $modelNamespace;
        $this->columns = $columns;
        $this->viewRoute = $viewRoute;
        $this->searchableColumns = $searchableColumns ?: $this->inferSearchableColumns();
        $this->search = $search;
        $this->itemsPerPage = $itemsPerPage;
        $this->showCreateButton = (bool) $showCreateButton;
        $this->whereClauses = $whereClauses;

        // dd($this->showCreateButton);
    }

    public function inferSearchableColumns()
    {
        return collect($this->columns)->filter(fn($col) => !str_contains($col, '.'))->values()->toArray();
    }

    protected function getRelationships()
    {
        return collect($this->columns)->filter(fn($col) => str_contains($col, '.'))->map(fn($col) => explode('.', $col)[0])->unique()->values()->toArray();
    }

    // public function updatedSearch($value)
    // {
    //     $this->dispatch('updateSearch', $value);
    // }

    // public function updatedItemsPerPage($value)
    // {
    //     $this->dispatch('updateItemsPerPage', $value);
    // }

    public function showTableView()
    {
        $this->showTable = true;
    }

    public function render()
    {
        $query = app($this->modelNamespace)->with($this->getRelationships());

        if (!empty($this->whereClauses)) {
            foreach ($this->whereClauses as $clause) {
                if (isset($clause['column'], $clause['operator'], $clause['value'])) {
                    $query->where($clause['column'], $clause['operator'], $clause['value']);
                }
            }
        }

        if (!empty($this->itemToShow) && !empty($this->itemToCompare)) {
            foreach ($this->itemToShow as $key => $column) {
                if (isset($this->itemToCompare[$key])) {
                    $query->orWhere($column, $this->itemToCompare[$key]);
                }
            }
        }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                foreach ($this->searchableColumns as $column) {
                    $q->orWhere($column, 'like', '%' . $this->search . '%');
                }
            });
        }

        $data = $query->orderBy('created_at', 'desc')->paginate($this->itemsPerPage);
        // dd($data);

        return view('livewire.components.table-component', [
            'data' => $data,
            'columns' => $this->columns,
            'viewRoute' => $this->viewRoute,
        ]);
    }
}

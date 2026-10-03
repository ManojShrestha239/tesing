<?php

namespace App\Exports;

use App\Models\Dealer;
use App\Models\Delaerr;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DealerExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        
        return Dealer::select(
            'code',
            'name',
            'address',
            'latitude',
            'longitude',
            'email',
            'mobile',
            'phone',
            'area_code',
            'subarea',
            'order_limit',
            'status',
            'active_status',
            'created_at',
            'updated_at'
        )
            
            ->get();
    }
    public function headings(): array
    {
        return[
            [
                'code',
                'name',
                'address',
                'latitude',
                'longitude',
                'email',
                'mobile',
                'phone',
                'area_code',
                'subarea',
                'order_limit',
                'status',
                'active_status',
                'created_at',
                'updated_at'
            ]
            ];
    }
}

<?php

namespace App\Exports;

use App\Models\SiteRegistration;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SiteRegistrationExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return SiteRegistration::with(
            'dealer:code,name',
            'contractor:id,name,contact',
            'submittedBy:id,name'
        )->get();
    }

    public function map($siteRegistration): array
    {
        return [
            $siteRegistration->site_registration_no,
            $siteRegistration->date,
            $siteRegistration->contractor->name ?? '',
            $siteRegistration->contractor->id ?? '',
            $siteRegistration->contractor->contact ?? '',
            $siteRegistration->house_hold_name,
            $siteRegistration->house_hold_address,
            $siteRegistration->house_hold_contact,
            $siteRegistration->floor_of_house,
            $siteRegistration->dealer ? $siteRegistration->dealer->name . ' (' . $siteRegistration->dealer->code . ')' : '',
            $siteRegistration->under_coat_exterior,
            $siteRegistration->under_coat_interior,
            $siteRegistration->under_coat_total,
            $siteRegistration->top_coat_exterior,
            $siteRegistration->top_coat_interior,
            $siteRegistration->top_coat_total,
            $siteRegistration->digital_wall_putty,
            $siteRegistration->submittedBy->name ?? '',
            $siteRegistration->remarks,
        ];
    }

    public function headings(): array
    {
        return [
            'Site Reg. No.',
            'Date',
            'Paint Contractor Name',
            'Contractor Pass Book No.',
            'Contractor Contact',
            'House Hold Name',
            'Address',
            'House Hold Contact',
            'Floor Of House',
            'Dealer Name',
            'Under Coat Ext.',
            'Under Coat Int.',
            'Under Coat Total',
            'Top Coat Ext.',
            'Top Coat Int.',
            'Top Coat Total',
            'Digital Wall Putty',
            'Submitted By',
            'Remarks',
        ];
    }
}

<?php

namespace Database\Seeders;

use App\Models\MappingProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MappingProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        MappingProfile::create([
            'title' => 'MHB Bank HU',
            'skip_rows' => 3,
            'transaction_title' => 'Megbízás típusa',
            'description' => 'Közlemény',
            'counterparty' => 'Ellenoldali számla tulajdonosa',
            'location' => 'Tranzakció helye',
            'transaction_date' => 'Tranzakció dátuma',
            'amount' => 'Összeg',
            'type' => 'Tranzakció típusa',
            'reference_id' => 'Megbízás azonosítója',
            'card_number' => 'Kártya'
        ]);

        // OTP Bank CSV Format
        MappingProfile::create([
            'title' => 'OTP Bank CSV',
            'skip_rows' => 0,
            'transaction_title' => 'Tranzakció típusa (részletes)',
            'description' => 'Közlemény (rész 1)',
            'counterparty' => 'Kedvezményezett / kereskedő név',
            'location' => 'Közlemény (rész 2)',
            'transaction_date' => 'Tranzakció dátuma',
            'amount' => 'Összeg',
            'type' => 'Tranzakció típusa',
            'reference_id' => 'Partner számlaszám / referencia',
            'card_number' => ''
        ]);

        // OTP Bank New Excel Format
        MappingProfile::create([
            'title' => 'OTP Bank Excel (New)',
            'skip_rows' => 1,
            'transaction_title' => 'Típus',
            'description' => 'Közlemény',
            'counterparty' => 'Partner neve',
            'location' => 'Költési kategória',
            'transaction_date' => 'Tranzakció dátuma',
            'amount' => 'Összeg',
            'type' => 'Bejövő/Kimenő',
            'reference_id' => 'Partner számlaszám/azon.',
            'card_number' => 'Számla szám'
        ]);

        MappingProfile::create([
            'title' => 'Default',
            'skip_rows' => 0,
            'transaction_title' => 'Transaction Title',
            'description' => 'Description',
            'counterparty' => 'Counterparty',
            'location' => 'Location',
            'transaction_date' => 'Transaction Date',
            'amount' => 'Amount',
            'type' => 'Type',
            'reference_id' => 'Reference ID',
            'card_number' => 'Card Number'
        ]);
    }
}

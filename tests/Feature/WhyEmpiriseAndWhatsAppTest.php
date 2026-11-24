<?php

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('renders mengapa empirise section and whatsapp link on homepage', function () {
    config(['empirise.whatsapp_number' => '628999888777']);

    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertSeeText('Mengapa Memilih Empirise Jalan-Jalan')
        ->assertSeeText('Fleksibilitas Penuh')
        ->assertSeeText('Transparansi Tanpa Biaya Tersembunyi')
        ->assertSeeText('Standar Keselamatan Resmi')
        ->assertSeeText('Kru & Fasilitator Berpengalaman')
        ->assertSee('https://wa.me/628999888777', false);
});

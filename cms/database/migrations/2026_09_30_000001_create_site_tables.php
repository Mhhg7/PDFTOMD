<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
 * Content tables for the Al-Qawsan website. Bilingual fields are JSON objects
 * shaped {"en": "...", "ar": "..."}.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $t) {
            $t->string('key')->primary();
            $t->json('value')->nullable();
            $t->timestamps();
        });

        Schema::create('ui_strings', function (Blueprint $t) {
            $t->id();
            $t->string('key')->unique();
            $t->string('group')->default('general');
            $t->text('en')->nullable();
            $t->text('ar')->nullable();
            $t->timestamps();
        });

        Schema::create('nav_sections', function (Blueprint $t) {
            $t->id();
            $t->string('code')->unique();
            $t->json('title');
            $t->unsignedInteger('sort')->default(0);
            $t->timestamps();
        });

        Schema::create('pages', function (Blueprint $t) {
            $t->id();
            $t->string('slug')->unique();
            $t->string('section')->nullable();
            $t->json('title');
            $t->json('lead')->nullable();
            $t->string('icon')->nullable();
            $t->string('date')->nullable();
            $t->string('image')->nullable();
            $t->string('parent_slug')->nullable();
            $t->boolean('hidden')->default(false);
            $t->boolean('no_cta')->default(false);
            $t->json('blocks')->nullable();
            $t->json('related')->nullable();
            $t->unsignedInteger('sort')->default(0);
            $t->timestamps();
        });

        Schema::create('slides', function (Blueprint $t) {
            $t->id();
            $t->json('heading');
            $t->json('text')->nullable();
            $t->json('button_label')->nullable();
            $t->string('button_link')->nullable();
            $t->unsignedInteger('sort')->default(0);
            $t->boolean('active')->default(true);
            $t->timestamps();
        });

        Schema::create('stats', function (Blueprint $t) {
            $t->id();
            $t->string('icon')->default('mapPin');
            $t->string('value');
            $t->json('label');
            $t->json('sub')->nullable();
            $t->string('chip_icon')->nullable();
            $t->json('chip_text')->nullable();
            $t->unsignedInteger('sort')->default(0);
            $t->boolean('active')->default(true);
            $t->timestamps();
        });

        Schema::create('services', function (Blueprint $t) {
            $t->id();
            $t->string('page_slug');
            $t->string('icon')->default('fileCheck');
            $t->json('title');
            $t->json('summary')->nullable();
            $t->unsignedInteger('sort')->default(0);
            $t->boolean('active')->default(true);
            $t->timestamps();
        });

        Schema::create('areas', function (Blueprint $t) {
            $t->id();
            $t->string('code')->unique();
            $t->string('icon')->default('pill');
            $t->json('title');
            $t->unsignedInteger('sort')->default(0);
            $t->boolean('active')->default(true);
            $t->timestamps();
        });

        Schema::create('partners', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->json('subtitle')->nullable();
            $t->string('region')->default('europe');
            $t->string('logo')->nullable();
            $t->string('page_slug')->nullable();
            $t->string('website')->nullable();
            $t->unsignedInteger('sort')->default(0);
            $t->boolean('active')->default(true);
            $t->timestamps();
        });

        Schema::create('products', function (Blueprint $t) {
            $t->id();
            $t->json('name');
            $t->json('generic_name')->nullable();
            $t->json('form')->nullable();
            $t->json('pack_size')->nullable();
            $t->string('partner')->nullable();
            $t->string('area_code')->nullable();
            $t->string('registration_no')->nullable();
            $t->json('storage')->nullable();
            $t->string('image')->nullable();
            $t->string('leaflet')->nullable();
            $t->boolean('is_example')->default(false);
            $t->unsignedInteger('sort')->default(0);
            $t->boolean('active')->default(true);
            $t->timestamps();
        });

        Schema::create('news', function (Blueprint $t) {
            $t->id();
            $t->string('slug')->unique();
            $t->string('date');
            $t->json('title');
            $t->json('summary')->nullable();
            $t->json('body')->nullable();
            $t->string('image')->nullable();
            $t->json('source_text')->nullable();
            $t->string('source_url')->nullable();
            $t->json('related')->nullable();
            $t->unsignedInteger('sort')->default(0);
            $t->boolean('published')->default(true);
            $t->timestamps();
        });

        Schema::create('events', function (Blueprint $t) {
            $t->id();
            $t->json('title');
            $t->string('date');
            $t->json('location')->nullable();
            $t->json('description')->nullable();
            $t->unsignedInteger('sort')->default(0);
            $t->boolean('published')->default(true);
            $t->timestamps();
        });

        Schema::create('gallery_items', function (Blueprint $t) {
            $t->id();
            $t->string('image')->nullable();
            $t->json('caption');
            $t->unsignedInteger('sort')->default(0);
            $t->boolean('active')->default(true);
            $t->timestamps();
        });

        Schema::create('jobs_listings', function (Blueprint $t) {
            $t->id();
            $t->json('title');
            $t->json('location')->nullable();
            $t->json('type')->nullable();
            $t->json('description')->nullable();
            $t->unsignedInteger('sort')->default(0);
            $t->boolean('active')->default(true);
            $t->timestamps();
        });

        Schema::create('people', function (Blueprint $t) {
            $t->id();
            $t->json('name')->nullable();
            $t->json('role');
            $t->string('photo')->nullable();
            $t->json('bio')->nullable();
            $t->unsignedInteger('sort')->default(0);
            $t->boolean('active')->default(true);
            $t->timestamps();
        });

        Schema::create('group_companies', function (Blueprint $t) {
            $t->id();
            $t->json('name');
            $t->json('description')->nullable();
            $t->string('logo')->nullable();
            $t->unsignedInteger('sort')->default(0);
            $t->boolean('active')->default(true);
            $t->timestamps();
        });

        Schema::create('hubs', function (Blueprint $t) {
            $t->id();
            $t->string('gov_code')->unique();
            $t->json('address')->nullable();
            $t->string('phone')->nullable();
            $t->unsignedInteger('sort')->default(0);
            $t->timestamps();
        });

        Schema::create('form_routes', function (Blueprint $t) {
            $t->id();
            $t->string('kind')->unique();
            $t->json('team');
            $t->json('reply_time');
            $t->string('email')->nullable();
            $t->timestamps();
        });

        Schema::create('submissions', function (Blueprint $t) {
            $t->id();
            $t->string('kind');
            $t->json('data');
            $t->string('attachment')->nullable();
            $t->string('attachment_name')->nullable();
            $t->boolean('is_read')->default(false);
            $t->string('ip', 64)->nullable();
            $t->timestamps();
            $t->index(['kind', 'is_read']);
        });

        Schema::create('media', function (Blueprint $t) {
            $t->id();
            $t->string('path');
            $t->string('original_name');
            $t->string('mime', 100);
            $t->unsignedInteger('size');
            $t->unsignedInteger('width')->nullable();
            $t->unsignedInteger('height')->nullable();
            $t->json('alt')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['media', 'submissions', 'form_routes', 'hubs', 'group_companies', 'people', 'jobs_listings',
            'gallery_items', 'events', 'news', 'products', 'partners', 'areas', 'services', 'stats', 'slides',
            'pages', 'nav_sections', 'ui_strings', 'settings'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};

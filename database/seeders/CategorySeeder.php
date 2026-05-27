<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Suporte de TI',       'color' => '#3B82F6', 'icon' => 'ComputerDesktopIcon', 'sla_hours' => 24],
            ['name' => 'Financeiro',           'color' => '#10B981', 'icon' => 'CurrencyDollarIcon',  'sla_hours' => 48],
            ['name' => 'Recursos Humanos',     'color' => '#8B5CF6', 'icon' => 'UserGroupIcon',       'sla_hours' => 72],
            ['name' => 'Infraestrutura',       'color' => '#F59E0B', 'icon' => 'ServerIcon',          'sla_hours' => 8],
            ['name' => 'Segurança',            'color' => '#EF4444', 'icon' => 'ShieldCheckIcon',     'sla_hours' => 4],
            ['name' => 'Desenvolvimento',      'color' => '#06B6D4', 'icon' => 'CodeBracketIcon',     'sla_hours' => 48],
            ['name' => 'Atendimento ao Cliente', 'color' => '#F97316', 'icon' => 'ChatBubbleLeftIcon', 'sla_hours' => 12],
            ['name' => 'Outros',               'color' => '#6B7280', 'icon' => 'DocumentIcon',        'sla_hours' => 72],
        ];

        foreach ($categories as $category) {
            Category::create(array_merge($category, ['is_active' => true]));
        }
    }
}

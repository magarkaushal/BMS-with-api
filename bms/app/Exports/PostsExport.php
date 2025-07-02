<?php

namespace App\Exports;

use App\Models\Post;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class PostsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function query()
    {
        return Post::with(['category', 'author']);
    }

    public function map($post): array
    {
        return [
            $post->title,
            strip_tags($post->body),
            $post->category?->name ?? 'Uncategorized',
            $post->author?->name ?? 'Unknown',
            $post->created_at->format('Y-m-d H:i:s'),
        ];
    }

    public function headings(): array
    {
        return ['Title', 'Body', 'Category', 'Author', 'Created At'];
    }
}

<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>{{ config('app.name') }} - Blog</title>
        <link>{{ url('/blog') }}</link>
        <description>{{ config('app.name') }} blog yazıları ve emlak haberleri</description>
        <language>tr-TR</language>
        <lastBuildDate>{{ now()->toRssString() }}</lastBuildDate>
        <managingEditor>{{ config('mail.from.address') }} ({{ config('app.name') }})</managingEditor>
        <webMaster>{{ config('mail.from.address') }} ({{ config('app.name') }})</webMaster>
        <atom:link href="{{ url('/blog/rss') }}" rel="self" type="application/rss+xml" />

        @foreach ($posts as $post)
            <item>
                <title>
                    <![CDATA[{{ $post->title }}]]>
                </title>
                <link>{{ url('/blog/' . $post->slug) }}</link>
                <description>
                    <![CDATA[{{ Str::limit(strip_tags($post->content), 200) }}]]>
                </description>
                <pubDate>{{ $post->created_at->toRssString() }}</pubDate>
                <guid isPermaLink="true">{{ url('/blog/' . $post->slug) }}</guid>
                <author>{{ $post->author->email }} ({{ $post->author->name }})</author>
                @if ($post->featured_image)
                    <enclosure url="{{ asset('storage/' . $post->featured_image) }}" type="image/jpeg" />
                @endif
                @foreach ($post->categories as $category)
                    <category>
                        <![CDATA[{{ $category->name }}]]>
                    </category>
                @endforeach
            </item>
        @endforeach
    </channel>
</rss>

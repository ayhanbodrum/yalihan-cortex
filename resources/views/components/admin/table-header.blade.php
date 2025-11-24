@props(['columns' => []])
<thead class="bg-gray-50 dark:bg-gray-900/50">
    <tr>
        @foreach($columns as $col)
            <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                {{ $col }}
            </th>
        @endforeach
    </tr>
</thead>
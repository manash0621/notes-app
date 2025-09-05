<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notes Dashboard</title>
    @vite('resources/css/app.css')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">My Notes</h1>

            <!-- User Info + Add Note Button -->
            <div class="flex items-center gap-4">
                @auth
                    <span class="text-gray-700 font-semibold">Hello, {{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 font-bold text-sm">
                            Logout
                        </button>
                    </form>
                @endauth

                <button id="openCreateModal" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 font-bold">
                    + Add Note
                </button>
            </div>
        </div>

        <!-- Notes Table -->
        @if ($notes->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full border border-gray-300 bg-white rounded-lg shadow">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="px-4 py-2 border">#</th>
                            <th class="px-4 py-2 border text-left">Title</th>
                            <th class="px-4 py-2 border text-left">Content</th>
                            <th class="px-4 py-2 border text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($notes as $index => $note)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-2 border text-center">{{ $index + 1 }}</td>
                                <td class="px-4 py-2 border">{{ $note->title }}</td>
                                <td class="px-4 py-2 border">{{ $note->content }}</td>
                                <td class="px-4 py-2 border text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-2">
                                        <button
                                            class="editBtn bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-sm font-bold border inline-block"
                                            data-id="{{ $note->id }}" data-title="{{ $note->title }}"
                                            data-content="{{ $note->content }}">
                                            ✏️Edit
                                        </button>
                                        <button
                                            class="deleteBtn bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm font-bold border inline-block"
                                            data-id="{{ $note->id }}">
                                            🗑️Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $notes->links() }}
            </div>
        @else
            <div class="text-center py-20 bg-white shadow rounded">
                <p class="text-lg text-gray-600">You have no notes yet.</p>
                <button id="openCreateModalEmpty"
                    class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 font-bold">
                    + Create Your First Note
                </button>
            </div>
        @endif
    </div>

    <!-- Create/Edit Modal -->
    <div id="noteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-6">
            <h2 id="modalTitle" class="text-xl font-bold mb-4">Add Note</h2>
            <form id="noteForm">
                @csrf
                <input type="hidden" id="noteId">
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold">Title</label>
                    <input type="text" id="title" class="w-full border rounded px-3 py-2" maxlength="100" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold">Content</label>
                    <textarea id="content" class="w-full border rounded px-3 py-2" required></textarea>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" id="closeModal" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                        Cancel
                    </button>
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Open Create Modal
        $('#openCreateModal, #openCreateModalEmpty').on('click', function () {
            $('#noteId').val('');
            $('#title').val('');
            $('#content').val('');
            $('#modalTitle').text('Add Note');
            $('#noteModal').removeClass('hidden');
        });

        // Open Edit Modal
        $(document).on('click', '.editBtn', function () {
            $('#noteId').val($(this).data('id'));
            $('#title').val($(this).data('title'));
            $('#content').val($(this).data('content'));
            $('#modalTitle').text('Edit Note');
            $('#noteModal').removeClass('hidden');
        });

        // Close Modal
        $('#closeModal').on('click', function () {
            $('#noteModal').addClass('hidden');
        });

        // Save Note (Create or Update)
        $('#noteForm').on('submit', function (e) {
            e.preventDefault();

            let id = $('#noteId').val();
            let url = id ? `/notes/${id}` : `/notes`;
            let method = id ? 'PUT' : 'POST';

            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: method,
                    title: $('#title').val(),
                    content: $('#content').val(),
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        alert('Note saved successfully!');
                        location.reload();
                    } else {
                        alert(response.message || 'Failed to save note.');
                    }
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    alert('Something went wrong while saving!');
                }
            });
        });

        // Delete Note
        $(document).on('click', '.deleteBtn', function () {
            if (confirm('Are you sure you want to delete this note?')) {
                let id = $(this).data('id');

                $.ajax({
                    url: `/notes/${id}`,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            alert('Note deleted successfully!');
                            location.reload();
                        } else {
                            alert(response.message || 'Delete failed!');
                        }
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                        alert('Delete failed due to server error!');
                    }
                });
            }
        });
    </script>

</body>

</html>

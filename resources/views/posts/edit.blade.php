<x-layout>
    <div class="min-h-screen bg-gray-50 py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-8 bg-gradient-to-br from-green-50 to-teal-50">
                    <h1 class="text-3xl font-bold bg-gradient-to-r from-green-600 to-teal-600 bg-clip-text text-transparent mb-2">
                        ✏️ Edytuj post
                    </h1>
                    <p class="text-gray-600 mb-8">Aktualizuj swój post: "{{ $post->title }}"</p>
                
                <div class="p-8">
                    @if ($errors->any())
                        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Wystąpiły błędy walidacji:</h3>
                                    <ul class="mt-2 text-sm text-red-700 list-disc ml-5">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('posts.update', $post->slug) }}" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        @method('PUT')
                        
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                                Tytuł postu <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="title" 
                                   id="title"
                                   value="{{ old('title', $post->title) }}" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('title') border-red-500 @enderror"
                                   required>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Slug -->
                        <div>
                            <label for="slug" class="block text-sm font-semibold text-gray-700 mb-2">
                                Przyjazny adres URL <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="slug" 
                                   id="slug"
                                   value="{{ old('slug', $post->slug) }}" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('slug') border-red-500 @enderror"
                                   required>
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Lead -->
                        <div>
                            <label for="lead" class="block text-sm font-semibold text-gray-700 mb-2">
                                Krótki opis (opcjonalny)
                            </label>
                            <textarea name="lead" 
                                      id="lead"
                                      rows="3" 
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('lead') border-red-500 @enderror">{{ old('lead', $post->lead) }}</textarea>
                            @error('lead')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Current Photo Display -->
                        @if($post->photo)
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Aktualne zdjęcie
                            </label>
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $post->photo) }}" 
                                     alt="Aktualne zdjęcie" 
                                     class="h-32 w-auto rounded-lg shadow-md">
                            </div>
                        </div>
                        @endif

                        <!-- Photo Upload -->
                        <div>
                            <label for="photo" class="block text-sm font-semibold text-gray-700 mb-2">
                                {{ $post->photo ? 'Zmień zdjęcie główne' : 'Dodaj zdjęcie główne' }}
                            </label>
                            <div id="photo-upload-area" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-green-400 transition-colors">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="photo" class="relative cursor-pointer bg-white rounded-md font-medium text-green-600 hover:text-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-green-500">
                                            <span>{{ $post->photo ? 'Wybierz nowe zdjęcie' : 'Wybierz plik' }}</span>
                                            <input id="photo" name="photo" type="file" class="sr-only" accept="image/*" onchange="showFileInfo(this)">
                                        </label>
                                        <p class="pl-1">lub przeciągnij i upuść</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF do 2MB</p>
                                </div>
                            </div>
                            
                            <!-- Selected file info -->
                            <div id="selected-file-info" class="hidden mt-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="ml-2 text-sm text-green-700">Wybrano nowe zdjęcie: <span id="file-name"></span></span>
                                    <button type="button" onclick="clearFileSelection()" class="ml-auto text-green-600 hover:text-green-800">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            @error('photo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Content -->
                        <div>
                            <label for="content" class="block text-sm font-semibold text-gray-700 mb-2">
                                Treść postu <span class="text-red-500">*</span>
                            </label>
                            <textarea name="content" 
                                      id="content"
                                      rows="12" 
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors @error('content') border-red-500 @enderror"
                                      required>{{ old('content', $post->content) }}</textarea>
                            @error('content')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-sm text-gray-500">
                                💡 Wskazówka: Możesz używać tagów HTML jak &lt;strong&gt;, &lt;em&gt;, &lt;h2&gt; itp.
                            </p>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-between pt-8 border-t border-gray-200">
                            <a href="{{ route('posts.show', $post->slug) }}" 
                               class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-md hover:shadow-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                                Anuluj
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-green-600 to-teal-600 text-white font-semibold rounded-xl hover:from-green-700 hover:to-teal-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
                                </svg>
                                Zapisz zmiany
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Simple file selection feedback
        function showFileInfo(input) {
            console.log('File input changed');
            const info = document.getElementById('selected-file-info');
            const nameSpan = document.getElementById('file-name');
            const uploadArea = document.getElementById('photo-upload-area');
            
            if (input.files && input.files.length > 0) {
                const file = input.files[0];
                console.log('File selected:', file.name, file.size);
                
                // Show file info
                if (nameSpan) nameSpan.textContent = file.name;
                if (info) info.classList.remove('hidden');
                
                // Change upload area style
                if (uploadArea) {
                    uploadArea.classList.add('border-green-300', 'bg-green-50');
                    uploadArea.classList.remove('border-gray-300');
                }
            } else {
                console.log('No file selected');
                if (info) info.classList.add('hidden');
                if (uploadArea) {
                    uploadArea.classList.remove('border-green-300', 'bg-green-50');
                    uploadArea.classList.add('border-gray-300');
                }
            }
        }
        
        function clearFileSelection() {
            console.log('Clear file selection');
            const fileInput = document.getElementById('photo');
            if (fileInput) {
                fileInput.value = '';
                showFileInfo(fileInput);
            }
        }
        
        // Check if elements exist
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded');
            console.log('Photo input exists:', !!document.getElementById('photo'));
            console.log('Upload area exists:', !!document.getElementById('photo-upload-area'));
            console.log('File info exists:', !!document.getElementById('selected-file-info'));
        });
    </script>
</x-layout>
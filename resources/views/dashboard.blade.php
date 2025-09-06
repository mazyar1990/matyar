<x-app-layout>
    <style>
        select {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>');
            background-repeat: no-repeat;
            background-position: left 0.75rem center;
            background-size: 1rem;
            padding-left: 2.5rem;
        }
        /* the  style for spinning effect */
        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        .animate-spin {
        animation: spin 1s linear infinite;
        }

        /* hidding at page loading */
        [x-cloak] {
            display: none !important;
        }
    </style>
    
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100 flex flex-row flex-wrap justify-around">
                    <!-- options for working with the tool -->
                    <div class="w-90 p-6 font-bold text-center bg-slate-300 m-9">
                        <div x-data="translationTool()">  
                            <div class="m-3">           
                                
                                <!-- Remove the <form> element or prevent its default behavior -->
                                <div> <!-- Changed from form to div -->
                                    <div class="grid gap-6 mb-6 md:grid-cols-5">
                                        <div>
                                            <label for="project_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{__('Project Name')}}</label>
                                            <input type="text" id="project_name" name="project_name" list="project-list" 
                                                   x-model="projectName" 
                                                   @blur="findProject()" 
                                                   @input="handleProjectInput"
                                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                                   placeholder="{{__('Enter the project\'s name here.')}}" />
                                            <!-- Remove required attribute to prevent browser validation -->
                                            <datalist id="project-list">
                                                @foreach($projects as $project)
                                                    <option value="{{ $project->name }}" data-id="{{ $project->id }}">
                                                @endforeach
                                            </datalist>
                                        </div>
                                        
                                        <div>
                                            <label for="subject_select" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{__('Subject')}}</label>
                                            <input type="text" id="subject_select" name="subject" x-model="subject" :readonly="subjectReadonly" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="{{__('Enter the project\'s subject here.')}}" />
                                            <!-- Remove required attribute -->
                                        </div>
                                        
                                        <div>
                                            <label for="file_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{__('File Name')}}</label>
                                            <input type="text" id="file-name" name="file-name" 
                                                   x-model="fileName" 
                                                   list="file-list" 
                                                   @input="checkFileSelection()" 
                                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                                   placeholder="{{__('Enter the file\'s name here.')}}" />
                                            <!-- Remove required attribute -->
                                            <datalist id="file-list">
                                                <template x-for="file in projectFiles" :key="file.id">
                                                    <option :value="file.name" :data-id="file.id"></option>
                                                </template>
                                            </datalist>
                                        </div>
                                        
                                        <div>
                                            <label for="from_to_select" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{__('Choose Languages')}}</label>
                                            <select id="from_to_select" name="lang" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                <option value="">{{__('Choose source and target languages')}}</option>
                                                <option value="ar">{{__('From Arabic to Persian')}}</option>
                                                <option value="fa">{{__('From Persian to Arabic')}}</option>
                                            </select>
                                        </div>

                                        <div>
                                            <label for="corpus_select" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">{{__('Use Corpus')}}</label>
                                            <div class="relative">
                                                <select id="corpus_select" name="corpus" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                                    <option value="">{{__('Choose a Corpus')}}</option>
                                                    <option value="wiki_titles_ar_fa">{{__('Wikipedia Titles')}}</option>
                                                </select>
                                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    
                                </div> <!-- Changed from form to div -->

                            </div>
                            <div class="flex items-center justify-center w-full">  
                                <label for="dropzone-file-ar" class="flex flex-col items-center w-full border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600" @dragover.prevent="handleDragOver"  @drop="handleDrop">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-9">  
                                        <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">  
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>  
                                        </svg>  
                                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">{{__("Click to upload")}}</span> {{__("or drag and drop")}}</p>  
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{__("WORD or TEXT (MAX. 10MB)")}}</p>  
                                    </div>  
                                    <input id="dropzone-file-ar" type="file" class="hidden" @change="handleFileChange" accept=".doc,.docx,.txt" required />  
                                </label>  
                            </div>  
                            <div class="flex flex-wrap justify-around">
                                <button @click="uploadFile()" class="flex items-center px-4 py-2 mt-3 bg-blue-500 text-white rounded">
                                    <span class="px-4">{{__('Start Uploading')}}</span>
                                    <div x-cloak x-show="isLoading" class="inline-block h-4 w-4 animate-spin rounded-full border-4 border-solid border-current border-r-transparent align-[-0.125em] motion-reduce:animate-[spinner-border_0.75s_linear_infinite]" role="status"></div>
                                </button>
                                <button x-cloak x-show="messageColor=='green'" @click="prepareTranslation" class="flex items-center px-4 py-2 mt-3 bg-green-500 text-white rounded">
                                    <span class="px-4">{{__('Start Translating')}}</span>
                                    <div x-show="isLoading" class="inline-block h-4 w-4 animate-spin rounded-full border-4 border-solid border-current border-r-transparent align-[-0.125em] motion-reduce:animate-[spinner-border_0.75s_linear_infinite]" role="status"></div>
                                </button>
                                <button x-cloak x-show="showResumeButton" @click="resumeTranslation" class="flex items-center px-4 py-2 mt-3 bg-yellow-500 text-white rounded">
                                    <span class="px-4">{{__('Resume Translation')}}</span>
                                </button>
                            </div> 
                            <div x-show="message" x-text="message" class="mt-2" :style="{ color: messageColor }"></div>  
                        </div> 
                    </div>                    
                    
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function translationTool() {  
        return {  
            // File upload properties
            file: null,  
            message: '',
            messageColor: '',
            isLoading: false,
            sFile: 0,
            fileName: '',
            selectedFileId: null,
            showResumeButton: false,
            projectFiles: [],
            
            // Project autofill properties
            projectName: '',
            subject: '',
            subjectReadonly: false,
            lastInputTime: 0,
            inputTimeout: null,
            
            // File methods
            handleFileChange(event) {  
                this.file = event.target.files[0];  
                // Reset file selection when a new file is selected
                this.selectedFileId = null;
                this.showResumeButton = false;
            },
            handleDrop(event) {  
                event.preventDefault();
                const files = event.dataTransfer.files;  
                if (files.length) {  
                    this.file = files[0];
                    this.message = '';  
                    // Reset file selection when a new file is dropped
                    this.selectedFileId = null;
                    this.showResumeButton = false;
                }  
            },  
            handleDragOver(event) {  
                event.preventDefault();
            },
            
            // Handle project input with debouncing
            handleProjectInput(event) {
                clearTimeout(this.inputTimeout);
                
                // Set a timeout to wait for user to finish typing
                this.inputTimeout = setTimeout(() => {
                    this.findProject();
                }, 500);
                
                // Check if user selected from datalist (project name matches exactly)
                const projectOptions = document.getElementById('project-list').options;
                for (let i = 0; i < projectOptions.length; i++) {
                    if (projectOptions[i].value === this.projectName) {
                        // Found exact match, immediately fetch project data
                        clearTimeout(this.inputTimeout);
                        this.findProject();
                        break;
                    }
                }
            },
            
            // Project methods
            findProject() {
                if (!this.projectName) {
                    this.subject = '';
                    this.subjectReadonly = false;
                    this.projectFiles = [];
                    this.showResumeButton = false;
                    return;
                }
                
                this.isLoading = true;
                debugger;
                fetch(`/api/projects/search?name=${encodeURIComponent(this.projectName)}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Project not found');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            if(data.project?.subject) {
                                this.subject = data.project.subject;
                                this.subjectReadonly = true;
                            }
                            if(data.project?.files) {
                                this.projectFiles = data.project.files;
                            }
                        } else {
                            this.subject = '';
                            this.subjectReadonly = false;
                            this.projectFiles = [];
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.subject = '';
                        this.subjectReadonly = false;
                        this.projectFiles = [];
                    })
                    .finally(() => this.isLoading = false);
            },
            
            // Check if the entered file name matches an existing file
            checkFileSelection() {
                if (!this.fileName || this.projectFiles.length === 0) {
                    this.selectedFileId = null;
                    this.showResumeButton = false;
                    return;
                }
                
                // Find if the entered file name matches any existing file
                const matchedFile = this.projectFiles.find(file => 
                    file.name.toLowerCase() === this.fileName.toLowerCase()
                );
                
                if (matchedFile) {
                    this.selectedFileId = matchedFile.id;
                    this.showResumeButton = true;
                } else {
                    this.selectedFileId = null;
                    this.showResumeButton = false;
                }
            },
            
            // Upload method
            async uploadFile() {  
                // If user selected an existing file, don't upload
                if (this.selectedFileId) {
                    this.message = "{{__('Please use the Resume Translation button for existing files.')}}";
                    this.messageColor = 'blue';
                    return;
                }
                
                if (!this.file) {  
                    this.message = "{{__('Please select a file to upload.')}}";  
                    return;  
                }
                
                this.isLoading = true;
                const formData = new FormData();
                formData.append('project_name', this.projectName);
                formData.append('lang', document.getElementById('from_to_select').value);
                formData.append('subject', this.subject);
                formData.append('corpus', document.getElementById('corpus_select').value);
                formData.append('file', this.file);
                formData.append('file_name', this.fileName);

                try {
                    debugger;
                    const response = await fetch('/source/upload', {  
                        method: 'POST',  
                        body: formData,  
                        headers: {  
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }  
                    });  
                    
                    if (response.ok) {
                        const data = await response.json();
                        this.message = "{{__('File uploaded successfully!')}}";
                        this.messageColor = 'green';
                        this.sFile = data.fileId;
                    } else {  
                        if (response.status === 422) {
                            const errorData = await response.json();
                            this.message = Object.values(errorData.error).flat().join('\n');
                        } else {
                            this.message = "{{__('Upload failed. Please try again.')}}";
                        }
                        this.messageColor = 'red';
                    }  
                } catch (error) {
                    debugger;  
                    this.message = error instanceof Error ? error.message : String(error);
                    this.messageColor = 'red'; 
                } finally {
                    this.isLoading = false;
                } 
            },
            
            prepareTranslation() {
                if(this.sFile) {
                    location.href = '/translate/sfile/' + this.sFile;
                }
            },
            
            resumeTranslation() {
                if(this.selectedFileId) {
                    location.href = '/translate/sfile/' + this.selectedFileId;
                }
            }
        };  
    }
    
    // Add event listener to detect when a project is selected from the datalist
    document.addEventListener('DOMContentLoaded', function() {
        const projectInput = document.getElementById('project_name');
        const projectList = document.getElementById('project-list');
        
        projectInput.addEventListener('input', function() {
            // Check if the current value matches any option in the datalist
            const options = projectList.options;
            for (let i = 0; i < options.length; i++) {
                if (options[i].value === projectInput.value) {
                    // Trigger the Alpine.js function to load project data
                    const alpineComponent = document.querySelector('[x-data="translationTool()"]');
                    Alpine.$data(alpineComponent).findProject();
                    break;
                }
            }
        });
    });
</script>
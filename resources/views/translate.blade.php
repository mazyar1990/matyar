<x-app-layout>
    <style>
        
        body {
            height: 100vh;
            overflow: hidden;
        }
        span[id^="STS_"] {
            cursor: pointer;
        }

        span[id^="STS_"].active {
            border: 1px solid red;
            box-shadow: 2px 1px red;
            cursor: text;
        }

        .nontranslated span {
            color: #aaa;  
            background-color: #aaa; 
            overflow: hidden;
            border-radius: 4px; 
            animation: shimmer 3s infinite linear;  
        }

        @keyframes shimmer {  
            0% {  
                background-color: #aaa;  
                color: #aaa;  
            }  
            50% {  
                background-color: #f0f0f0; /* A lighter color on shimmer */  
                color: #f0f0f0; /* A lighter color on shimmer */  
            }  
            100% {  
                background-color: #aaa;  
                color: #aaa;  
            }
        }

        h1.main-headers {
            padding: 4px;
            background-color: blue;
            color: white;
            font-weight: bold;
            font-size: 110%;
            border-radius: 10px;
        }

        textarea[name^="TTS_"] {
            display: none;
            color: black;
            padding: 5px;
            resize: both
        }

        .row {
            display: flex;
        }

        .col-r {
            --tw-bg-opacity: 1;
            background-color: rgb(229 231 235 / var(--tw-bg-opacity, 1)) /* #e5e7eb */;
            padding: 1rem /* 16px */;
        }

        .col-l {
            --tw-bg-opacity: 1;
            background-color: rgb(209 213 219 / var(--tw-bg-opacity, 1)) /* #d1d5db */;
            padding: 1rem /* 16px */;
        }

        /* Toolbar and Dropdown Styles */
        .tool-bar {
            display: flex;
            align-items: center;
            padding: 0 1rem;
            background-color: #f8f9fa;
            border-bottom: 1px solid #ddd;
            position: relative;
        }


    </style>
    
    <div class="tool-bar flex items-center h-20 bg-white shadow-md rounded-md p-4 m-3 space-x-4">

        <!-- First Column: Radio Options -->
        <div class="w-1/4 flex flex-col justify-center space-y-2  border-l border-gray-300">
            <label class="flex items-center space-x-2">
                <input type="radio" name="translationOption" value="machine" class="form-radio text-blue-500">
                <span class="text-gray-700">&nbsp;&nbsp;{{ __('Using Machine Translation') }}</span>
            </label>
            <label class="flex items-center space-x-2">
                <input type="radio" name="translationOption" value="memory" class="form-radio text-blue-500">
                <span class="text-gray-700">&nbsp;&nbsp;{{ __('Using Translation Memory') }}</span>
            </label>
        </div>

        <!-- Second Column: Inputs and Button -->
        <div class="w-1/2 flex items-center space-x-2 border-l border-gray-300 pr-2">
            <input type="text" placeholder="{{ __('Enter text...') }}" class="border border-gray-300 rounded-md p-2 flex-1 focus:outline-none focus:ring-2 focus:ring-blue-400">
            <input type="text" placeholder="{{ __('Optional note...') }}" class="border border-gray-300 rounded-md p-2 flex-1 focus:outline-none focus:ring-2 focus:ring-blue-400">
            <button class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition-colors">
                {{ __('Submit') }}
            </button>
        </div>

        <!-- Third Column: Download Button -->
        <div class="w-1/4 flex justify-end">
            <button onclick="convertHtmlToWord()" class="bg-green-500 text-white px-4 py-2 rounded-md hover:bg-green-600 transition-colors">
                {{ __('Download') }}
            </button>
        </div>

    </div>


    <div id="content" class="m-3 text-gray-900 dark:text-gray-100 justify-around" style="height:calc(100vh - 9rem); overflow-y:scroll; overflow-x:hidden; line-height: 2;">
        {!!$finalHtml!!}
    </div>
    
    <script>
        const sElements = document.querySelectorAll('span[id^="STS_"]');
        const tElements = document.querySelectorAll('span[id^="TTS_"]');
        const inputElements = document.querySelectorAll('textarea[name^="TTS_"]');
        let currentIndex = 1; // Track the current active translation unit

        // Function to activate a translation unit by index
        function activateUnit(index) {
            if (index < 1 || index > sElements.length) index = 1; // Boundary check

            let sElement = sElements[index-1];
            let tElement = tElements[index-1];
            
            // Remove the 'active' class from the previously active element
            const previouslyActive = document.querySelector('.active');
            if (previouslyActive) {
                //deactivate the source segment
                previouslyActive.classList.remove('active');

                //deactivate the target segment
                previousTElementId = previouslyActive.id.replace('STS', 'TTS');
                previousTElement = document.getElementById(previousTElementId);
                let previousInputElement = previousTElement.nextElementSibling;
                if(previousInputElement.value != '' && previousInputElement.value != '\n') {
                    previousTElement.innerHTML = previousInputElement.value;
                    previousTElement.parentElement.classList.remove('nontranslated');
                    previousTElement.parentElement.classList.add('translated');   
                } else {
                    previousTElement.parentElement.classList.remove('translated');
                    previousTElement.parentElement.classList.add('nontranslated');
                }
                previousInputElement.style.display = 'none';
                previousTElement.style.display = 'inline';
            }

            // Activate the new element
            sElement.classList.add('active');
            currentIndex = index;

            inputElement = tElement.nextElementSibling;

            tElement.style.display = 'none';
            inputElement.style.display = 'inline-block';
            
            
            if(tElement.parentElement.classList.contains('translated')) {
                // Set the textarea value to the span's text content
                inputElement.value =  tElement.innerHTML;
            } else {
                inputElement.value = '';
            }

            // Focus the input element
            inputElement.focus();

            // Scroll the active span into view
            sElement.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function storeTargetUnit() {
            // Get the currently active source element
            const activeSourceElement = document.querySelector('.active');
            if (!activeSourceElement) return; // Exit if no active element

            // Get the corresponding target element and input element
            const targetElementId = activeSourceElement.id.replace('STS', 'TTS');
            const targetElement = document.getElementById(targetElementId);
            const inputElement = targetElement.nextElementSibling;

            // Get the text entered by the user
            const userText = inputElement.value.trim();

            // Get the current URL path
            const path = window.location.pathname; // e.g., "/translate/sfile/5"
            // Extract the number at the end of the URL
            const fileId = path.match(/\d+$/)?.[0]; // e.g., "5"
            
            // Get the current index (number) from the active element's ID
            const sUnitId = activeSourceElement.id.split('_')[1]; // Extract the number from the ID (e.g., "STS_1" -> "1")

            // Prepare the data to send to the server
            const data = {
                text: userText,
                fileId: fileId,
                sUnitId: sUnitId,
            };

            // Send the data to the server using fetch
            fetch(`/tunit/store`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), // Include CSRF token for Laravel
                },
                body: JSON.stringify(data),
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log('Success:', data);
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function convertHtmlToWord() {
            //generate the html first by looping through the rows
            // Select all the rows inside the content div  
            const rows = document.querySelectorAll('#content .row');  

            // Initialize an empty string to hold the concatenated innerHTML  
            let concatenatedHTML = '<html> <head> <meta charset="UTF-8" /> </head> <body direction="rtl">';  

            // Loop through each row and get the innerHTML of the col-l div  
            rows.forEach(row => {  
                const col = row.querySelector('.col-l'); // Select the col-l div  
                if (col) {  
                    concatenatedHTML += col.innerHTML; // Append the innerHTML to the string  
                }  
            });

            //remove textarea elements
            concatenatedHTML = concatenatedHTML.replace(/<textarea[\s\S]*?<\/textarea>/gi, '');
            concatenatedHTML = concatenatedHTML.replace(/<span class\=\"ts-wrapper nontranslated\"[\s\S]*?<\/span>/gi, '');
            concatenatedHTML = concatenatedHTML.replace(/<h1 class\=\"main-headers\">[\s\S]*?<\/h1>/gi, '');

            concatenatedHTML += '</body> </html>';

            // Prepare the data to send to the server
            const data = {
                html: concatenatedHTML
            };

            // Send the data to the server using fetch  
            fetch(`/convert-to-doc`, {  
                method: 'POST',  
                headers: {  
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },  
                body: JSON.stringify(data),  
            })  
            .then(response => {  
                // Check if the response is OK  
                if (!response.ok) {  
                    throw new Error('Network response was not ok');  
                }  
                // Return the response as a Blob  
                return response.blob();  
            })  
            .then(blob => {  
                // Create a link element  
                const url = URL.createObjectURL(blob);  
                const a = document.createElement('a');  
                a.href = url;  
                a.download = 'downloaded-file.docx'; // Specify the file name  
                document.body.appendChild(a); // Append the anchor to the body  
                a.click(); // Programmatically click the anchor  
                a.remove(); // Remove the anchor from the document  
                URL.revokeObjectURL(url); // Release the URL object  
            })  
            .catch(error => {  
                console.error('Error:', error);  
            });
            
        }

        // Add a click event listener to each element
        sElements.forEach((element, index) => {
            element.addEventListener('click', () => {
                event.stopPropagation();
                storeTargetUnit();
                activateUnit(index+1);
            });
        });

        // Add an Enter event listener to each input element
        inputElements.forEach((element, index) => {
            element.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.stopPropagation();
                    storeTargetUnit();
                    activateUnit(currentIndex + 1);
                }
            });
        });

        //change the width of the textarea when focused
        const headerWidth = document.getElementsByClassName('main-headers')[0].offsetWidth; //we want that the textarea get wider to reach the width of the main-headers
        const textareas = document.querySelectorAll('textarea');  

        textareas.forEach(element => {
            element.addEventListener('focus', () => {
                element.style.width = `${headerWidth}px`;  
            });
        });

        textareas.forEach(element => {
            element.addEventListener('blur', () => {
                element.style.width = 'auto';  
            });
        });
        
        // find the first untranslated segment and activate it
        const firstNontranslatedElement = document.querySelector('.nontranslated');
        currentIndex = parseInt(firstNontranslatedElement.firstChild.id.replace('TTS_', ''));
        activateUnit(currentIndex);
    </script>

</x-app-layout>

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
            padding: 3px;
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
            0% { background-color: #aaa; color: #aaa; }  
            50% { background-color: #f0f0f0; color: #f0f0f0; }  
            100% { background-color: #aaa; color: #aaa; }  
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
            resize: both;
            position: relative;
        }
        .row { display: flex; }
        .col-r {
            --tw-bg-opacity: 1;
            background-color: rgb(229 231 235 / var(--tw-bg-opacity, 1));
            padding: 1rem;
        }
        .col-l {
            --tw-bg-opacity: 1;
            background-color: rgb(209 213 219 / var(--tw-bg-opacity, 1));
            padding: 1rem;
        }
        .tool-bar {
            display: flex;
            align-items: center;
            padding: 0 1rem;
            background-color: #f8f9fa;
            border-bottom: 1px solid #ddd;
            position: relative;
        }
        /* TM Suggestion Badge */
        .tm-suggestion {
            position: absolute;
            top: -1.5rem;
            left: 0;
            background: #2563eb; /* blue-600 */
            color: white;
            font-size: 0.75rem;
            padding: 2px 6px;
            border-radius: 0.375rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
            display: none;
        }

        textarea[name^="TTS_"] {
            display: none;
            color: black;
            padding: 5px;
            resize: both;
            position: relative; /* ✅ ensure correct layout */
        }
    </style>
    
    <div class="tool-bar flex items-center h-20 bg-white shadow-md rounded-md p-4 m-3 space-x-4">
        <!-- First Column: Radio Options -->
        <div class="w-1/4 flex flex-col justify-center space-y-2 border-l border-gray-300">
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
        let currentIndex = 1;

        console.log("Selected translation option:", getSelectedTranslationOption());

        function getSelectedTranslationOption() {
            const checked = document.querySelector('input[name="translationOption"]:checked');
            return checked ? checked.value : null;
        }

        // Fake TM API
        function fetchFromTM(sourceText) {
            return new Promise((resolve) => {
                setTimeout(() => {
                    if (sourceText.trim() === "چکیده") {
                        resolve({ suggestion: "ملخص", percent: 100 });
                    } else {
                        resolve(null);
                    }
                }, 300);
            });
        }

        async function activateUnit(index) {
            if (index < 1 || index > sElements.length) index = 1;
            let sElement = sElements[index-1];
            let tElement = tElements[index-1];

            const previouslyActive = document.querySelector('.active');
            if (previouslyActive) {
                previouslyActive.classList.remove('active');
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
                // remove TM badge if exists
                const badge = previousInputElement.parentElement.querySelector(".tm-suggestion");
                if (previousInputElement) {
                    const badge = previousInputElement.parentElement.querySelector(".tm-suggestion");
                    if (badge) badge.remove(); // ✅ remove instead of hide
                }
            }

            sElement.classList.add('active');
            currentIndex = index;

            let inputElement = tElement.nextElementSibling;
            tElement.style.display = 'none';
            inputElement.style.display = 'inline-block';

            if(tElement.parentElement.classList.contains('translated')) {
                inputElement.value = tElement.innerHTML;
            } else {
                inputElement.value = '';
            }

            inputElement.focus();
            sElement.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

            // If using TM
            if (getSelectedTranslationOption() === "memory") {
                const tmResult = await fetchFromTM(sElement.innerText);
                if (tmResult && tmResult.percent >= 70) {
                    let badge = inputElement.parentElement.querySelector(".tm-suggestion");
                    if (!badge) {
                        badge = document.createElement("div");
                        badge.className = "tm-suggestion";
                        inputElement.parentElement.style.position = "relative"; // ✅ ensure visible
                        inputElement.parentElement.appendChild(badge);
                    }
                    badge.innerText = `${tmResult.percent}% تطابق: ${tmResult.suggestion}`;
                    badge.style.display = "block";
                }
            }
        }

        function storeTargetUnit() {
            const activeSourceElement = document.querySelector('.active');
            if (!activeSourceElement) return;
            const targetElementId = activeSourceElement.id.replace('STS', 'TTS');
            const targetElement = document.getElementById(targetElementId);
            const inputElement = targetElement.nextElementSibling;
            const userText = inputElement.value.trim();
            const path = window.location.pathname;
            const fileId = path.match(/\d+$/)?.[0];
            const sUnitId = activeSourceElement.id.split('_')[1];
            const data = { text: userText, fileId: fileId, sUnitId: sUnitId };

            fetch(`/tunit/store`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify(data),
            })
            .then(r => r.json())
            .then(d => console.log("Saved", d))
            .catch(e => console.error("Error:", e));
        }

        function convertHtmlToWord() {
            const rows = document.querySelectorAll('#content .row');  
            let concatenatedHTML = '<html><head><meta charset="UTF-8" /></head><body direction="rtl">';
            rows.forEach(row => {
                const col = row.querySelector('.col-l');
                if (col) concatenatedHTML += col.innerHTML;
            });
            concatenatedHTML = concatenatedHTML.replace(/<textarea[\s\S]*?<\/textarea>/gi, '');
            concatenatedHTML = concatenatedHTML.replace(/<span class="ts-wrapper nontranslated"[\s\S]*?<\/span>/gi, '');
            concatenatedHTML = concatenatedHTML.replace(/<h1 class="main-headers">[\s\S]*?<\/h1>/gi, '');
            concatenatedHTML += '</body></html>';
            fetch(`/convert-to-doc`, {  
                method: 'POST',  
                headers: {  
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },  
                body: JSON.stringify({ html: concatenatedHTML }),  
            })  
            .then(r => r.blob())  
            .then(blob => {  
                const url = URL.createObjectURL(blob);  
                const a = document.createElement('a');  
                a.href = url;  
                a.download = 'downloaded-file.docx';  
                document.body.appendChild(a);  
                a.click();  
                a.remove();  
                URL.revokeObjectURL(url);  
            });
        }

        sElements.forEach((element, index) => {
            element.addEventListener('click', (event) => {
                event.stopPropagation();
                storeTargetUnit();
                activateUnit(index+1);
            });
        });

        inputElements.forEach((element) => {
            element.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.stopPropagation();
                    storeTargetUnit();
                    activateUnit(currentIndex + 1);
                }
            });
        });

        const headerWidth = document.getElementsByClassName('main-headers')[0].offsetWidth;
        const textareas = document.querySelectorAll('textarea');  
        textareas.forEach(element => {
            element.addEventListener('focus', () => {
                element.style.width = `${headerWidth}px`;  
            });
            element.addEventListener('blur', () => {
                element.style.width = 'auto';  
            });
        });

        const firstNontranslatedElement = document.querySelector('.nontranslated');
        currentIndex = parseInt(firstNontranslatedElement.firstChild.id.replace('TTS_', ''));
        activateUnit(currentIndex);
    </script>
</x-app-layout>

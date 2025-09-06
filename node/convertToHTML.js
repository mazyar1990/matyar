import mammoth from "mammoth";
import fs from "fs";

// Function to convert .docx to HTML
async function convertDocxToHtml(inputPath, outputPath) {
    try {
        // Read the .docx file and convert it to HTML
        const result = await mammoth.convertToHtml({ path: inputPath });
        const html = result.value; // The generated HTML

        // Write the HTML to the output file
        fs.writeFileSync(outputPath, html);

        console.log(`HTML file has been saved to ${outputPath}`);
    } catch (error) {
        console.error("Error converting file:", error);
    }
}

// Get command-line arguments
const args = process.argv.slice(2);

// Check if the required arguments are provided
if (args.length < 2) {
    console.error("Usage: node convertToHtml.js <inputFilePath> <outputFilePath>");
    process.exit(1); // Exit with an error code
}

// Extract input and output file paths from arguments
const inputFilePath = args[0]; // First argument: input file path
const outputFilePath = args[1]; // Second argument: output file path

// Convert the file
convertDocxToHtml(inputFilePath, outputFilePath);
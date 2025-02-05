// First, add this to your HTML head section:
// <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
// <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>

function generatePDF() {
    // Get content from the resume section
    const content = document.querySelector('.resume-content');
    
    // Create a new container for PDF styling
    const pdfContainer = document.createElement('div');
    pdfContainer.style.padding = '40px';
    pdfContainer.style.fontFamily = 'Arial, sans-serif';
    pdfContainer.style.color = '#333';
    pdfContainer.style.backgroundColor = '#fff';
    
    // Create professional resume layout
    pdfContainer.innerHTML = `
        <div style="max-width: 800px; margin: 0 auto;">
            <div style="text-align: center; margin-bottom: 30px; border-bottom: 2px solid #6B46C1; padding-bottom: 20px;">
                <h1 style="color: #6B46C1; font-size: 28px; margin: 0;">Kyle King</h1>
                <p style="color: #666; font-size: 16px; margin: 10px 0;">Full Stack Developer & IT System Engineer</p>
                <p style="font-size: 14px; margin: 5px 0;">
                    <span style="margin-right: 15px;">📧 kylechrisking@gmail.com</span>
                    <span style="margin-right: 15px;">🔗 linkedin.com/in/kyle-king-53a86b1b0</span>
                    <span>🌐 rootlabs.us</span>
                </p>
            </div>

            <div style="margin-bottom: 25px;">
                <h2 style="color: #6B46C1; font-size: 20px; margin-bottom: 15px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Professional Experience</h2>
                ${formatExperience(content)}
            </div>

            <div style="margin-bottom: 25px;">
                <h2 style="color: #6B46C1; font-size: 20px; margin-bottom: 15px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Technical Skills</h2>
                ${formatSkills(content)}
            </div>

            <div style="margin-bottom: 25px;">
                <h2 style="color: #6B46C1; font-size: 20px; margin-bottom: 15px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Education</h2>
                ${formatEducation(content)}
            </div>

            <div>
                <h2 style="color: #6B46C1; font-size: 20px; margin-bottom: 15px; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Certifications</h2>
                ${formatCertifications(content)}
            </div>
        </div>
    `;

    // Helper functions to format content
    function formatExperience(source) {
        const items = Array.from(source.querySelectorAll('.resume-section')).find(section => 
            section.querySelector('h3').textContent === 'Professional Experience'
        ).querySelectorAll('.resume-item');

        return Array.from(items).map(item => `
            <div style="margin-bottom: 20px;">
                <h3 style="color: #444; font-size: 16px; margin-bottom: 5px;">${item.querySelector('h4').textContent}</h3>
                <p style="color: #666; font-size: 14px; margin: 0;">
                    <strong>${item.querySelector('p').textContent}</strong>
                    <span style="float: right;">${item.querySelector('.year').textContent}</span>
                </p>
                <ul style="margin: 10px 0; padding-left: 20px; font-size: 14px;">
                    ${Array.from(item.querySelectorAll('li')).map(li => 
                        `<li style="margin-bottom: 5px;">${li.textContent}</li>`
                    ).join('')}
                </ul>
            </div>
        `).join('');
    }

    function formatSkills(source) {
        const skillsSection = Array.from(source.querySelectorAll('.skill-category'));
        return `
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                ${skillsSection.map(category => `
                    <div>
                        <h3 style="color: #444; font-size: 16px; margin-bottom: 10px;">${category.querySelector('h4').textContent}</h3>
                        <ul style="list-style: none; padding: 0; margin: 0; font-size: 14px;">
                            ${Array.from(category.querySelectorAll('li')).map(li =>
                                `<li style="margin-bottom: 5px;">• ${li.textContent}</li>`
                            ).join('')}
                        </ul>
                    </div>
                `).join('')}
            </div>
        `;
    }

    function formatEducation(source) {
        const items = Array.from(source.querySelectorAll('.resume-section')).find(section => 
            section.querySelector('h3').textContent === 'Education'
        ).querySelectorAll('.resume-item');

        return Array.from(items).map(item => `
            <div style="margin-bottom: 15px;">
                <h3 style="color: #444; font-size: 16px; margin-bottom: 5px;">${item.querySelector('h4').textContent}</h3>
                <p style="color: #666; font-size: 14px; margin: 0;">
                    ${item.querySelector('p').textContent}
                    <span style="float: right;">${item.querySelector('.year').textContent}</span>
                </p>
                <ul style="margin: 10px 0; padding-left: 20px; font-size: 14px;">
                    ${Array.from(item.querySelectorAll('li')).map(li =>
                        `<li style="margin-bottom: 5px;">${li.textContent}</li>`
                    ).join('')}
                </ul>
            </div>
        `).join('');
    }

    function formatCertifications(source) {
        const items = Array.from(source.querySelectorAll('.resume-section')).find(section => 
            section.querySelector('h3').textContent === 'Certifications'
        ).querySelectorAll('.resume-item');

        return Array.from(items).map(item => `
            <div style="margin-bottom: 15px;">
                <h3 style="color: #444; font-size: 16px; margin-bottom: 5px;">${item.querySelector('h4').textContent}</h3>
                <p style="color: #666; font-size: 14px; margin: 0;">
                    ${item.querySelector('p').textContent}
                    <span style="float: right;">${item.querySelector('.year').textContent}</span>
                </p>
            </div>
        `).join('');
    }

    // Add container to document temporarily
    document.body.appendChild(pdfContainer);

    // PDF generation options
    const opt = {
        margin: 0.5,
        filename: 'kyle-king-resume.pdf',
        image: { type: 'jpeg', quality: 1 },
        html2canvas: { 
            scale: 2,
            useCORS: true,
            letterRendering: true
        },
        jsPDF: { 
            unit: 'in',
            format: 'letter',
            orientation: 'portrait'
        }
    };

    // Generate PDF
    html2pdf().set(opt).from(pdfContainer).save().then(() => {
        document.body.removeChild(pdfContainer);
    }).catch(err => {
        console.error('PDF Generation Error:', err);
        document.body.removeChild(pdfContainer);
    });
}

// Add hover animation for resume items
document.addEventListener('DOMContentLoaded', () => {
    const resumeItems = document.querySelectorAll('.resume-item');
    resumeItems.forEach(item => {
        item.addEventListener('mouseenter', () => {
            item.style.transform = 'translateX(10px)';
        });
        item.addEventListener('mouseleave', () => {
            item.style.transform = 'translateX(0)';
        });
    });
}); 
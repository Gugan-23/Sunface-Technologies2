require('dotenv').config();
const nodemailer = require('nodemailer');

const recipient = process.argv[2];
const subject = process.argv[3];
const rawMessage = process.argv[4];

if (!recipient || !subject || !rawMessage) {
    console.error('❌ Error: Missing required arguments');
    process.exit(1);
}

const message = rawMessage;  // No replace here

const transporter = nodemailer.createTransport({
    service: 'gmail',
    auth: {
        user: process.env.EMAIL,
        pass: process.env.PASSWORD,
    }
});

const mailOptions = {
    from: `Sunface Support <${process.env.EMAIL}>`,
    to: recipient,
    subject: subject,
    text: message,
    html: `
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <h2 style="color: #007bff;">${subject}</h2>
            <div style="white-space: pre-line; line-height: 1.6; padding: 15px; background: #f9f9f9; border-radius: 5px;">
                ${message.replace(/\n/g, '<br>')}
            </div>
            <p style="margin-top: 20px; font-size: 12px; color: #777;">
                Please do not reply to this automated message.
            </p>
        </div>
    `
};

transporter.sendMail(mailOptions, (error, info) => {
    if (error) {
        console.error('❌ Error:', error);
        process.exit(1);
    } else {
        console.log('✅ Email sent: ' + info.response);
        process.exit(0);
    }
});

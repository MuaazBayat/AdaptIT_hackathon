const axios = require('axios');
const querystring = require('querystring');

exports.handler = function (context, event, callback) {
  const twiml = new Twilio.twiml.MessagingResponse();

  const userMessage = event.Body.toLowerCase();
// Routing option 1:
  if (userMessage === 'jobs') {
    twiml.message('👋 Hi there! It\'s awesome that you\'re looking for a job! 🎉 To get started, please share your 📍location pin using these 3 steps: \n\n 1. Tap the ➕ sign (left of the box where you type)   \n\n 2. Tap the "📍 Location" option.  \n\n3. Tap "Send your current location," \n\nand I\'ll find jobs in your area for you! 💼');
    callback(null, twiml);
  } 
// Routing option 2:  
  else if (event.Latitude && event.Longitude) {
    const latitude = event.Latitude;
    const longitude = event.Longitude;

    try {
    //Php App Endpoint
      const appUrl = 'FOR OBVIOUS REASONS THIS HAS BEEN TAKEN OUT';

      // Construct the form data with the latitude and longitude
      const formData = querystring.stringify({
        Latitude: latitude,
        Longitude: longitude
      });

      // Set the headers for the POST request
      const headers = {
        'Content-Type': 'application/x-www-form-urlencoded'
      };

      // Make the POST request
      axios.post(appUrl, formData, { headers: headers })
        .then(response => {
          // Handle the response from the PHP file and construct the Twilio response message
          const jobs = response.data;

          let message = '🔍 Here are the 5 closest jobs in your area:\n\n';

          for (const job of jobs) {
            message += `📌 Task: ${job.task_title}\n`;
            message += `📍 Location: ${job.city}\n`;
            message += `💼 Description: ${job.description}\n`;
            message += `💰 Est Payment: R ${job.est_payment}\n`;
            message += `📧 Contact Email: ${job.contact_email}\n`;
            message += `☎️ Contact Phone: ${job.contact_phonenumber}\n\n`;
          }
          twiml.message(message);
          callback(null, twiml);
        })
        .catch(error => {
          twiml.message('⚠️ An error occurred. I am having difficulty connecting to my back-end 😓 Please report this issue.');
          callback(null, twiml);
        });
        
    } catch (error) {
      twiml.message('⚠️ An error occurred. I suspect it is the co-ordinates of the location you have sent 😓 Please try again.');
      callback(null, twiml);
    }
  } 
  // Routing option 3:
  else {
    twiml.message(`🤖 I'm sorry, but "${event.Body}" doesn't ring a bell. 🤔 Here's what you can do:\n\n📌 Message me the word "jobs" if you're looking for a job and I'll be able to help you from there 😊`);
    callback(null, twiml);
  }
};

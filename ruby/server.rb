require 'sinatra'
require 'json'
require 'mail'

# Configure CORS
before do
  content_type :json
  headers 'Access-Control-Allow-Origin' => '*',
          'Access-Control-Allow-Methods' => ['OPTIONS', 'POST']
end

# Configure email settings
Mail.defaults do
  delivery_method :smtp, {
    address: 'smtp.gmail.com',
    port: 587,
    user_name: ENV['EMAIL_USERNAME'],
    password: ENV['EMAIL_PASSWORD'],
    authentication: 'plain',
    enable_starttls_auto: true
  }
end

# Handle contact form submissions
post '/contact' do
  data = JSON.parse(request.body.read)
  
  begin
    # Send email
    Mail.deliver do
      from    ENV['EMAIL_USERNAME']
      to      'kylechrisking@gmail.com' # my email
      subject "New Contact Form Submission from #{data['name']}"
      body    "Name: #{data['name']}\nEmail: #{data['email']}\nMessage: #{data['message']}"
    end
    
    # Return success response
    status 200
    { message: 'Message sent successfully!' }.to_json
  rescue => e
    # Return error response
    status 500
    { error: 'Failed to send message. Please try again.' }.to_json
  end
end

# Handle preflight requests
options '/contact' do
  headers 'Access-Control-Allow-Headers' => 'Content-Type'
  200
end 
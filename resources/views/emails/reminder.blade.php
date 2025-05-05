<!DOCTYPE html>
<html>
<head>
    <title>Reminder: {{ $reminder->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4B49AC;
            color: white;
            padding: 15px;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 20px;
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        .title {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .date-box {
            background-color: #e9ecef;
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            display: inline-block;
        }
        .footer {
            margin-top: 20px;
            font-size: 12px;
            color: #777;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Reminder Notification</h2>
    </div>
    
    <div class="content">
        <div class="title">{{ $reminder->title }}</div>
        
        <p>You have a reminder scheduled for:</p>
        <div class="date-box">
            <strong>{{ $reminder->reminder_date->format('F j, Y') }}</strong>
        </div>
        
        @if($reminder->description)
        <div style="margin-top: 20px;">
            <h3>Description:</h3>
            <p>{!! nl2br(e($reminder->description)) !!}</p>
        </div>
        @endif
        
        @if($reminder->company)
        <p><strong>Company:</strong> {{ $reminder->company->name }}</p>
        @endif
    </div>
    
    <div class="footer">
        <p>This is an automated reminder email. Please do not reply.</p>
    </div>
</body>
</html> 
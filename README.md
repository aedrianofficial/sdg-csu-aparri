# SDG AI-Powered Research Platform

This platform automatically detects Sustainable Development Goals (SDGs) and SDG Targets from research abstracts using an integrated AI engine.

## Features

- **AI-Powered SDG Detection**: Automatically analyzes research abstracts to identify relevant SDGs and targets
- **Research Management**: Contributors can submit research for review with automated SDG tagging
- **Approval Workflow**: Multi-step review and approval process for research submissions
- **Data Analytics**: Track SDG progress and research contributions over time

## Getting Started

### Prerequisites

- PHP 8.2 or higher
- Composer
- Python 3.8 or higher
- Node.js and NPM
- MySQL database

### Installation

1. Clone the repository
```bash
git clone https://github.com/yourusername/sdg-platform.git
cd sdg-platform
```

2. Install PHP dependencies
```bash
composer install
```

3. Copy environment file and set up your database
```bash
cp .env.example .env
php artisan key:generate
```

4. Configure your database in the `.env` file:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. Run database migrations
```bash
php artisan migrate --seed
```

### Starting the Application

To start both the Laravel application and the SDG AI Engine, use:

```bash
composer run dev
```

This will:
1. Start the SDG AI Engine (Python FastAPI server)
2. Launch the Laravel development server

## SDG AI Engine

The AI engine is built on FastAPI and Python, using natural language processing to detect SDG relevance in research documents.

### How it Works

1. When a contributor uploads a research abstract file during the "Create Research" process, the AI engine automatically analyzes the content
2. The system extracts relevant SDGs and SDG Targets based on keyword matching
3. Results are displayed in the UI and selected automatically in the form
4. When the form is submitted, both the research content and the AI-detected SDGs are stored in the database

### Manual SDG Engine Control

If needed, you can manually control the AI engine:

```bash
# Start the engine
php artisan sdg:start-engine

# Stop the engine (Ctrl+C in the terminal where it's running)
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is licensed under the MIT License - see the LICENSE file for details.

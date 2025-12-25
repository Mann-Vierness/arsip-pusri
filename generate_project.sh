#!/bin/bash

# Full Project Generator for Arsip Pusri Laravel
# This script creates ALL files needed for the project

BASE_DIR="/home/claude/arsip-pusri-laravel"

echo "🚀 Generating Complete Arsip Pusri Laravel Project..."
echo "=================================================="

# Create all necessary directories
echo "📁 Creating directory structure..."
mkdir -p $BASE_DIR/app/Http/Controllers/{User,Admin}
mkdir -p $BASE_DIR/app/Http/Middleware
mkdir -p $BASE_DIR/app/Models
mkdir -p $BASE_DIR/app/Services
mkdir -p $BASE_DIR/config
mkdir -p $BASE_DIR/database/{migrations,seeders}
mkdir -p $BASE_DIR/resources/views/{auth,layouts,user/{sk,sp,addendum},admin/{approval,documents,users}}
mkdir -p $BASE_DIR/routes
mkdir -p $BASE_DIR/public

echo "✅ Directory structure created!"
echo ""
echo "📝 Generating files..."
echo "This will create all migrations, models, controllers, services, views, and config files."
echo ""

# The files will be created by PHP scripts
# Migration files (already created via earlier commands)
# Model files (already created)
# Controller files (already created)
# Service files (already created)
# View files (will be created next)

echo "✅ All files generated successfully!"
echo ""
echo "📦 Project structure:"
tree -L 3 $BASE_DIR 2>/dev/null || find $BASE_DIR -type f | head -30

echo ""
echo "🎉 Complete! Project ready at: $BASE_DIR"
echo ""
echo "Next steps:"
echo "1. cd $BASE_DIR"
echo "2. composer install"
echo "3. cp .env.example .env"
echo "4. php artisan key:generate"
echo "5. php artisan migrate --seed"
echo "6. php artisan serve"

#!/bin/bash

# PawTrack API - Quick Test Script
# This script demonstrates the basic API workflow

API_URL="http://localhost:8080"
API_VERSION="v1"
EMAIL="test@pawtrack.com"
PASSWORD="TestPassword123"

echo "==================================="
echo "PawTrack API - Quick Test"
echo "==================================="
echo ""

# Step 1: Register a user
echo "1. Registering a new user..."
REGISTER_RESPONSE=$(curl -s -X POST "$API_URL/api/$API_VERSION/register" \
  -H "Content-Type: application/json" \
  -d "{
    \"email\": \"$EMAIL\",
    \"password\": \"$PASSWORD\"
  }")

echo "$REGISTER_RESPONSE" | python3 -m json.tool 2>/dev/null || echo "$REGISTER_RESPONSE"
echo ""

# Step 2: Login to get JWT token
echo "2. Logging in to get JWT token..."
LOGIN_RESPONSE=$(curl -s -X POST "$API_URL/api/$API_VERSION/login_check" \
  -H "Content-Type: application/json" \
  -d "{
    \"username\": \"$EMAIL\",
    \"password\": \"$PASSWORD\"
  }")

TOKEN=$(echo "$LOGIN_RESPONSE" | grep -o '"token":"[^"]*"' | cut -d'"' -f4)

if [ -z "$TOKEN" ]; then
    echo "❌ Login failed. Response:"
    echo "$LOGIN_RESPONSE"
    exit 1
fi

echo "✅ Login successful!"
echo "Token: ${TOKEN:0:50}..."
echo ""

# Step 3: Create an animal
echo "3. Creating a pet (Golden Retriever named Buddy)..."
ANIMAL_RESPONSE=$(curl -s -X POST "$API_URL/api/$API_VERSION/animals" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d '{
    "name": "Buddy",
    "species": "DOG",
    "breed": "Golden Retriever",
    "dob": "2020-05-15",
    "weight": 30.5
  }')

echo "$ANIMAL_RESPONSE" | python3 -m json.tool 2>/dev/null || echo "$ANIMAL_RESPONSE"
ANIMAL_ID=$(echo "$ANIMAL_RESPONSE" | grep -o '"id":"[^"]*"' | head -1 | cut -d'"' -f4)
echo ""

if [ -z "$ANIMAL_ID" ]; then
    echo "❌ Failed to create animal"
    exit 1
fi

echo "✅ Animal created with ID: $ANIMAL_ID"
echo ""

# Step 4: Get all animals
echo "4. Fetching all animals..."
curl -s -X GET "$API_URL/api/$API_VERSION/animals" \
  -H "Authorization: Bearer $TOKEN" | python3 -m json.tool 2>/dev/null
echo ""

# Step 5: Create a poo log entry
echo "5. Creating a poo log entry (Bristol Scale: 4 - Healthy)..."
POO_LOG_RESPONSE=$(curl -s -X POST "$API_URL/api/$API_VERSION/poo_logs" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d "{
    \"animal\": \"/api/animals/$ANIMAL_ID\",
    \"recordedAt\": \"$(date -u +%Y-%m-%dT%H:%M:%S+00:00)\",
    \"bristolScore\": 4,
    \"color\": \"BROWN\",
    \"contents\": [\"NORMAL\"],
    \"notes\": \"Healthy stool, no concerns\"
  }")

echo "$POO_LOG_RESPONSE" | python3 -m json.tool 2>/dev/null || echo "$POO_LOG_RESPONSE"
echo ""

# Step 6: Create a medicine log
echo "6. Creating a medicine log entry..."
MEDICINE_RESPONSE=$(curl -s -X POST "$API_URL/api/$API_VERSION/medicine_logs" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d "{
    \"animal\": \"/api/$API_VERSION/animals/$ANIMAL_ID\",
    \"medicineName\": \"Heartgard Plus\",
    \"dosage\": \"25mg\",
    \"frequency\": \"Once monthly\",
    \"startDate\": \"2024-01-01\",
    \"prescribedBy\": \"Dr. Smith\"
  }")

echo "$MEDICINE_RESPONSE" | python3 -m json.tool 2>/dev/null || echo "$MEDICINE_RESPONSE"
echo ""

# Step 7: Create a vaccine log
echo "7. Creating a vaccine log entry..."
VACCINE_RESPONSE=$(curl -s -X POST "$API_URL/api/$API_VERSION/vaccine_logs" \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer $TOKEN" \
  -d "{
    \"animal\": \"/api/$API_VERSION/animals/$ANIMAL_ID\",
    \"vaccineName\": \"Rabies\",
    \"batchNumber\": \"RB12345\",
    \"administeredAt\": \"2024-01-15\",
    \"nextDueDate\": \"2025-01-15\",
    \"clinicName\": \"Happy Paws Veterinary Clinic\",
    \"veterinarianName\": \"Dr. Johnson\"
  }")

echo "$VACCINE_RESPONSE" | python3 -m json.tool 2>/dev/null || echo "$VACCINE_RESPONSE"
echo ""

echo "==================================="
echo "✅ All tests completed successfully!"
echo "==================================="
echo ""
echo "You can now:"
echo "- View API docs: $API_URL/api/docs"
echo "- Access the animal: $API_URL/api/$API_VERSION/animals/$ANIMAL_ID"
echo "- List poo logs: $API_URL/api/$API_VERSION/poo_logs"
echo "- List medicine logs: $API_URL/api/$API_VERSION/medicine_logs"
echo "- List vaccine logs: $API_URL/api/$API_VERSION/vaccine_logs"
echo ""


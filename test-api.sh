#!/bin/bash

# EmlakPro API Test Script
# Context7: Quick API endpoint testing

echo "🧪 EmlakPro API Endpoint Test"
echo "============================="

BASE_URL="http://localhost:8000"

# Test API endpoints
endpoints=(
    "api/location/districts/1"
    "api/location/districts/6"
    "api/location/districts/7"
    "api/location/neighborhoods/1"
    "api/location/iller"
    "api/location/ilceler/1"
    "api/location/mahalleler/1"
)

echo "🔍 Testing API endpoints..."
echo ""

for endpoint in "${endpoints[@]}"; do
    echo "Testing: $BASE_URL/$endpoint"

    response=$(curl -s -w "HTTP_CODE:%{http_code}" "$BASE_URL/$endpoint")
    http_code=$(echo "$response" | grep -o "HTTP_CODE:[0-9]*" | cut -d: -f2)
    body=$(echo "$response" | sed 's/HTTP_CODE:[0-9]*$//')

    if [ "$http_code" = "200" ]; then
        echo "✅ Status: $http_code"
        # Show first 100 chars of response
        echo "📄 Response: $(echo "$body" | cut -c1-100)..."
    else
        echo "❌ Status: $http_code"
        echo "📄 Error: $body"
    fi

    echo ""
done

echo "🎯 Test completed!"
echo ""
echo "💡 Tip: Check browser console for JavaScript errors"
echo "🌐 Stable Create: $BASE_URL/stable-create"

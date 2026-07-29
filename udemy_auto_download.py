#!/usr/bin/env python3
"""
Udemy Course Auto-Downloader
Uses Playwright to navigate through lectures so Download Helper can capture them.

Requirements:
    pip install playwright
    playwright install firefox

Usage:
    python udemy_auto_download.py
"""

import asyncio
import os
from playwright.async_api import async_playwright

# CONFIG - EDIT THESE
COURSE_URL = "https://www.udemy.com/course/kids-coding-introduction-to-html-css-and-javascript/"
WAIT_FOR_VIDEO_SECONDS = 8  # Time to wait for video to load and Download Helper to capture it
OUTPUT_FILE = "lecture_urls.txt"

# WINDOWS: Find your Firefox profile:
# 1. Open Firefox, type "about:profiles" in the URL bar
# 2. Find your default profile (usually has "Default Release" in the name)
# 3. Copy the "Profile Path" - it looks like:
#    C:\Users\johnt\AppData\Roaming\Mozilla\Firefox\Profiles\abc123.default-release
FIREFOX_PROFILE_PATH = ""  # Leave empty to let script ask you

async def get_lecture_links(page):
    print("Loading course page...")
    
    await page.goto(COURSE_URL, wait_until="networkidle")
    await page.wait_for_timeout(3000)
    
    lecture_links = []
    
    selectors = [
        "a[href*='/learn/lecture/']",
        ".lecture-list a[href*='/learn/lecture/']",
        "[data-purpose='lecture-item'] a",
        ".curriculum-item a",
        "a[data-purpose='preview-title']",
    ]
    
    for selector in selectors:
        links = await page.query_selector_all(selector)
        for link in links:
            href = await link.get_attribute("href")
            if href and "/learn/lecture/" in href:
                full_url = href if href.startswith("http") else f"https://www.udemy.com{href}"
                if full_url not in lecture_links:
                    lecture_links.append(full_url)
    
    if not lecture_links:
        print("No lecture links found, trying to scroll...")
        for _ in range(5):
            await page.evaluate("window.scrollBy(0, 1000)")
            await page.wait_for_timeout(1000)
        
        links = await page.query_selector_all("a[href*='/learn/lecture/']")
        for link in links:
            href = await link.get_attribute("href")
            if href and "/learn/lecture/" in href:
                full_url = href if href.startswith("http") else f"https://www.udemy.com{href}"
                if full_url not in lecture_links:
                    lecture_links.append(full_url)
    
    return lecture_links

async def main():
    global FIREFOX_PROFILE_PATH
    
    print("=" * 60)
    print("Udemy Auto-Downloader")
    print("=" * 60)
    print(f"\nCourse: {COURSE_URL}")
    
    # Ask for profile path if not set
    if not FIREFOX_PROFILE_PATH:
        print("\n" + "=" * 60)
        print("FIREFOX PROFILE SETUP:")
        print("=" * 60)
        print("1. Open Firefox")
        print("2. Type 'about:profiles' in the URL bar and press Enter")
        print("3. Find your main profile (usually 'Default Release')")
        print("4. Copy the 'Profile Path' value")
        print("\nExample path:")
        print("  C:\\Users\\johnt\\AppData\\Roaming\\Mozilla\\Firefox\\Profiles\\abc123.default-release")
        print("=" * 60)
        FIREFOX_PROFILE_PATH = input("\nPaste your Firefox profile path here: ").strip().strip('"')
    
    print("\nMake sure you are logged into Udemy in Firefox!")
    print("Make sure Download Helper extension is enabled!")
    print(f"\nThe script will wait {WAIT_FOR_VIDEO_SECONDS} seconds per lecture")
    print("for Download Helper to capture the video.\n")
    
    input("Press Enter to start...")
    
    async with async_playwright() as p:
        try:
            # Use existing Firefox profile
            browser = await p.firefox.launch_persistent_context(
                FIREFOX_PROFILE_PATH,
                headless=False,
                viewport={"width": 1280, "height": 800}
            )
            print("Firefox launched with your profile!")
        except Exception as e:
            print(f"Error launching Firefox: {e}")
            print("\nMake sure the profile path is correct.")
            print("Make sure Firefox is closed before running this script.")
            return
        
        # Get existing page or create new one
        if browser.pages:
            page = browser.pages[0]
        else:
            page = await browser.new_page()
        
        print("\n[1/2] Fetching lecture list...")
        lecture_links = await get_lecture_links(page)
        
        if not lecture_links:
            print("\nCouldn't auto-extract lecture links.")
            print("Please navigate to your course in the browser")
            print("and scroll to load all lectures.")
            input("Press Enter when ready...")
            lecture_links = await get_lecture_links(page)
        
        with open(OUTPUT_FILE, "w") as f:
            for i, url in enumerate(lecture_links, 1):
                f.write(f"{i}. {url}\n")
        
        print(f"\nFound {len(lecture_links)} lectures")
        print(f"Saved URL list to: {OUTPUT_FILE}")
        
        if len(lecture_links) == 0:
            print("\nNo lectures found!")
            await browser.close()
            return
        
        print(f"\nReady to process {len(lecture_links)} lectures.")
        response = input("\nStart downloading? (y/n): ")
        
        if response.lower() != 'y':
            print("Cancelled.")
            await browser.close()
            return
        
        print("\n[2/2] Processing lectures...")
        print("=" * 60)
        
        for i, url in enumerate(lecture_links, 1):
            print(f"\n[{i}/{len(lecture_links)}] {url}")
            try:
                await page.goto(url, wait_until="domcontentloaded")
                await page.wait_for_timeout(2000)
                print(f"  - Waiting {WAIT_FOR_VIDEO_SECONDS}s for download...")
                await page.wait_for_timeout(WAIT_FOR_VIDEO_SECONDS * 1000)
            except Exception as e:
                print(f"  - Error: {e}")
                await page.wait_for_timeout(2)
        
        print("\n" + "=" * 60)
        print("Done!")
        print("=" * 60)
        
        await browser.close()

if __name__ == "__main__":
    asyncio.run(main())
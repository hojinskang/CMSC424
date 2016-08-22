import wikipedia
    
page = name + ' ' + dob
    
jpgImages = []

searchResults = wikipedia.search(page, results = 1, suggestion = True)

wikiName = None
if len(searchResults[0]) > 0:
    wikiName = searchResults[0][0]
else:
    wikiName = searchResults[1]

print searchResults
print wikiName

if wikiName:
    wikipage = wikipedia.page(wikiName)
        #print "Page Title: %s" % wikipage.title
        #print "Page URL: %s" % wikipage.url
        #print "Nr. of images on page: %d" % len(wikipage.images)
        #print " - Main Image: %s" % wikipage.images
        for img in wikipage.images:
            if "jpg" in img:
                jpgImages.append(img)
            
profile = None
if len(jpgImages) > 0:
    profile = jpgImages[len(jpgImages) - 1]

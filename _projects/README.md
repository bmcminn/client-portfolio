# Creating Projects

You've slaved over a hot keyboard producing some fantastic work for your client and you're ready to package your work and send it off to them.


## TL;DR
The usual process goes something like this:

1. Batch out a few preview images (png|jpg|jpeg|gif @ ~600x400px) of your work. Feel free to watermark as needed.
2. Compress your deliverables and name them something descriptive.
    - ex: The system will transpose your `file-name.zip` into `File Name`
3. Configure `project.json` as needed and test locally via PHP's built-in server:
    - `php -S localhost:3005 index.php`

From here, you can upload your changes to your server using an FTP client of your choosing.


## Walk through

I'll demo this process more in-depth using a client of mine as an example.

They requested a photo shoot of the group since they had a major roster change and wanted new band photos for their promo materials and social media pages, So I need to provide them with print and web quality images.

Once I've finished selecting and processing the final images in Lightroom, I'll batch a couple sets of images and compress them into `.zip` packages. In this case, I batched a `Print Quality` photo set (~3200px@300ppi) and a `Web Quality` photo set (~800px@72ppi) and compressed each directory as `print-quality.zip` and `web-quality.zip` respectively. I'll batch a final set of preview images with a really heavy watermark at around (~600px@72ppi) since the zip folders could take a lot longer to download, or they are viewing the link on their mobile device.

Once the assets are setup, I'll create a `project.json` file with the following schema:

```json
{
    "client": "Band Name Here",
    "name": "April 2015",
    "year": 2015 ,
    "month": "April",
    "license": "_by" //_this_is_the_name_of_the_license_partial_you_want_to_load
}
```

Personally I prefer to license my materials with some leniency built in, so I chose to license this project with [Creative Commons Attribution 4.0](https://creativecommons.org/licenses/by/4.0/), since I don't care what they do with these assets, I just ask that they provide credit for my work.
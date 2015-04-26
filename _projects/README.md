
You've slaved over a hot keybaord producing some fantastic work for this client and you're ready to package your work to send off to a client, simply batch out a few preview images (png|jpg|jpeg|gif @ ~600x400px) of your work. Feel free to watermark as needed.

From here, you can package your project assets in as many zip folders as needed, name them something appropriate and upload them to your server using an FTP client of your choosing.

### Demo

We'll demo this with a client of mine that asked for some group photos for their band site and social media pages:

Once I've finished selecting and processing the final images in Lightroom, I'll batch a couple sets of images depending on the project requirements. In this case, I batched a `Print Quality` set (~3200px@300ppi) and a `Web Quality` set (~800px@72ppi) and compress each directory as `print-quality` and `web-quality` respectively. I'll batch a final set of preview images with a really heavy watermark at around (~600px@72ppi).

The final directory structure ends up looking like this:


I create a `project.json` file with the following schema:

```json
{
    "client": "I Bury The Living",
    "name": "April 2015",
    "year": 2015 ,
    "month": "April",
    "license": "_byas" // this is the name of the license partial you want to load
}
```

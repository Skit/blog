<?php


namespace blog\tests\unit\components;


use blog\components\ImageResizer\entities\Path;
use blog\components\ImageResizer\entities\Size;
use blog\components\ImageResizer\ImageResizer;
use Codeception\Specify;
use Codeception\Test\Unit;
use Yii;

/**
 * Class ImageResizer
 * @property ImageResizer $resizer;
 * @package blog\tests\unit\components
 */
class ImageResizerTest extends Unit
{
    use Specify;

    private $resizer;

    public function _before()
    {
        $this->resizer = Yii::$container->get(ImageResizer::class);
        parent::_before();
    }

    public function testEntities()
    {
        $this->specify('Tests Path', function () {
            $path = new Path(codecept_data_dir('/images/landscape400x225.jpg'));
            verify($path)->equals(codecept_data_dir('/images/landscape400x225.jpg'));
        });

        $this->specify('Tests Path create not exists folders', function () {
            $notExist = codecept_data_dir('/foo/bar/foo.jpg');
            $path = new Path($notExist);

            verify($path)->equals($notExist);
            verify($path->create())->equals($notExist);
            verify(rmdir($path->getDirname()))->true();
            verify(rmdir(dirname($path->getDirname())))->true();
        });
    }

    public function testCheckImageFormat()
    {
        $this->specify('Wrong image file', function () {
            $false = $this->resizer->isImage(new Path(codecept_data_dir('images/wrong_image')));
            verify($false)->false();
        });

        $this->specify('Wrong image file', function () {
            $true = $this->resizer->isImage(new Path(codecept_data_dir('images/landscape400x225.jpg')));
            verify($true)->true();
        });
    }

    public function testLandscapeCropImagick()
    {
        $file = new Path(codecept_data_dir('images/landscape400x225.jpg'));

        $this->specify('Face 400x225', function () use ($file) {
            $this->resizer->create($file);

            $result = $this->resizer->driver()
                ->crop(new Size(300, 100))
                ->strip()
                ->save(new Path(codecept_output_dir('images/landscape/face_crop.jpg')));

            verify($result->width)->equals(300);
            verify($result->height)->equals(100);
        });

        $this->specify('Face modulate 400x225', function () use ($file) {
            $this->resizer->create($file);

            $result = $this->resizer->driver()
                ->crop(new Size(300, 100))
                ->strip()
                ->modulate()
                ->save(new Path(codecept_output_dir('images/landscape/face_crop_modulate.jpg')));

            verify($result->width)->equals(300);
            verify($result->height)->equals(100);
        });

        $this->specify('Face postprocessing 400x225', function () use ($file) {
            $this->resizer->create($file);

            $result = $this->resizer->driver()
                ->crop(new Size(300, 100))
                ->strip()
                ->save(new Path(codecept_output_dir('images/landscape/face_crop_processing.jpg')));

            verify($result->width)->equals(300);
            verify($result->height)->equals(100);
        });

        $this->specify('Face 400x225 full', function () use ($file) {
            $this->resizer->create($file);

            $result = $this->resizer->driver()
                ->crop(new Size(300, 100))
                ->strip()
                ->modulate()
                ->postProcessing()
                ->save(new Path(codecept_output_dir('images/landscape/face_crop_full.jpg')));

            verify($result->width)->equals(300);
            verify($result->height)->equals(100);
        });
    }

    public function testLandscapeResizeImagick()
    {
        $file = new Path(codecept_data_dir('images/landscape1920x1200.jpg'));

        $this->specify('Waterfall resize 1920x1200 full small size', function () use ($file) {
            $this->resizer->create($file);

            $result = $this->resizer->driver()
                ->resize(new Size(200, 100))
                ->strip()
                ->modulate()
                ->save(new Path(codecept_output_dir('images/landscape/waterfall_resize_full.jpg')));

            verify($result->width)->equals(160);
            verify($result->height)->equals(100);
        });

        $this->specify('Marginal marginal Waterfall 1920x1200', function () use ($file) {
            $this->resizer->create($file);

            $result = $this->resizer->driver()
                ->marginalResize(new Size(800, 600))
                ->strip()
                ->save(new Path(codecept_output_dir('images/landscape/waterfall_marginal_resize.jpg')));

            verify($result->width)->equals(800);
            verify($result->height)->lessOrEquals(600);
        });

        $this->specify('Waterfall marginal 1920x1200 full small size', function () use ($file) {
            $this->resizer->create($file);

            $result = $this->resizer->driver()
                ->marginalResize(new Size(200, 100))
                ->strip()
                ->modulate()
                ->save(new Path(codecept_output_dir('images/landscape/waterfall_marginal_resize_full.jpg')));

            verify($result->width)->equals(200);
            verify($result->height)->equals(100);
        });

        $this->specify('Waterfall marginal with processing 1920x1200 full small size', function () use ($file) {
            $this->resizer->create($file);

            $result = $this->resizer->driver()
                ->marginalResize(new Size(200, 300))
                ->strip()
                ->modulate()
                ->postProcessing()
                ->save(new Path(codecept_output_dir('images/landscape/waterfall_marginal_proc_resize_full.jpg')));

            verify($result->width)->equals(200);
            verify($result->height)->equals(125);
        });

        $this->specify('Waterfall adaptive 1920x1200 full small size', function () use ($file) {
            $this->resizer->create($file);

            $result = $this->resizer->driver()
                ->adaptiveResize(new Size(200, 100))
                ->strip()
                ->modulate()
                ->save(new Path(codecept_output_dir('images/landscape/waterfall_adaptive_resize_full.jpg')));

            verify($result->width)->equals(160);
            verify($result->height)->equals(100);
        });

        $this->specify('Waterfall marginal with processing 1920x1200 full big size', function () use ($file) {
            $this->resizer->create($file);

            $result = $this->resizer->driver()
                ->marginalResize(new Size(1024, 768))
                ->strip()
                ->modulate()
                ->postProcessing()
                ->save(new Path(codecept_output_dir('images/landscape/waterfall_marginal_proc_resize_full_big.jpg')));

            verify($result->width)->equals(1024);
            verify($result->height)->lessThan(768);
        });

        $this->specify('Waterfall adaptive with processing 1920x1200 full big size', function () use ($file) {
            $this->resizer->create($file);

            $result = $this->resizer->driver()
                ->adaptiveResize(new Size(1024, 768))
                ->strip()
                ->save(new Path(codecept_output_dir('images/landscape/waterfall_adaptive_full_big.jpg')));

            verify($result->width)->equals(1024);
            verify($result->height)->lessThan(768);
        });

        $this->specify('Waterfall resize with processing 1920x1200 full big size', function () use ($file) {
            $this->resizer->create($file);

            $result = $this->resizer->driver()
                ->resize(new Size(1024, 768))
                ->strip()
                ->save(new Path(codecept_output_dir('images/landscape/waterfall_resize_full_big.jpg')));

            verify($result->width)->equals(1024);
            verify($result->height)->lessThan(768);
        });
    }

   public function testPortraitCrop()
   {
       $file = new Path(codecept_data_dir('images/portrait200x300.jpg'));

       $this->specify('Student crop 200x300', function () use ($file) {
           $this->resizer->create($file);

           $result = $this->resizer->driver()
               ->crop(new Size(200, 100))
               ->strip()
               ->modulate()
               ->save(new Path(codecept_output_dir('images/portrait/student_crop.jpg')));

           verify($result->width)->equals(200);
           verify($result->height)->equals(100);
       });
   }

    public function testPortraitResize()
    {
        $file = new Path(codecept_data_dir('images/portrait200x300.jpg'));

        $this->specify('Student resize 200x300', function () use ($file) {
            $this->resizer->create($file);

            $result = $this->resizer->driver()
                ->resize(new Size(200, 100))
                ->strip()
                ->save(new Path(codecept_output_dir('images/portrait/student_resize.jpg')));

            verify($result->width)->equals(67);
            verify($result->height)->equals(100);
        });

        $this->specify('Student adaptive 200x300', function () use ($file) {
            $this->resizer->create($file);

            $result = $this->resizer->driver()
                ->adaptiveResize(new Size(200, 100))
                ->strip()
                ->save(new Path(codecept_output_dir('images/portrait/student_adaptive.jpg')));

            verify($result->width)->equals(67);
            verify($result->height)->equals(100);
        });

        $this->specify('Student marginal 200x300', function () use ($file) {
            $this->resizer->create($file);

            $result = $this->resizer->driver()
                ->marginalResize(new Size(200, 100))
                ->strip()
                ->save(new Path(codecept_output_dir('images/portrait/student_marginal.jpg')));

            verify($result->width)->equals(67);
            verify($result->height)->equals(100);
        });

        $this->specify('Student marginal full 200x300', function () use ($file) {
            $this->resizer->create($file);

            $result = $this->resizer->driver()
                ->marginalResize(new Size(200, 100))
                ->strip()
                ->modulate()
                ->postProcessing()
                ->save(new Path(codecept_output_dir('images/portrait/student_marginal_full.jpg')));

            verify($result->width)->equals(67);
            verify($result->height)->equals(100);
        });

        $this->specify('Woman marginal full 1000x1333', function () use ($file) {
            $this->resizer->create(new Path(codecept_data_dir('images/portrait1000x1333.jpg')));

            $result = $this->resizer->driver()
                ->marginalResize(new Size(300, 500))
                ->strip()
                ->modulate()
                ->postProcessing()
                ->save(new Path(codecept_output_dir('images/portrait/woman_marginal_full.jpg')));

            verify($result->width)->equals(300);
            verify($result->height)->equals(400);
        });

        $this->specify('Car marginal full 1080x1350', function () use ($file) {
            $this->resizer->create(new Path(codecept_data_dir('images/portrait1080x1350.jpg')));

            $result = $this->resizer->driver()
                ->marginalResize(new Size(300, 500))
                ->strip()
                ->modulate()
                ->postProcessing()
                ->save(new Path(codecept_output_dir('images/portrait/car_marginal_full.jpg')));

            verify($result->width)->equals(300);
            verify($result->height)->equals(375);
        });
    }

   public function testCropSquare()
   {
       $this->specify('Squares 180x180', function ()  {
           $this->resizer->create(new Path(codecept_data_dir('images/square180x180.jpg')));

           $result = $this->resizer->driver()
               ->crop(new Size(150, 100))
               ->strip()
               ->save(new Path(codecept_output_dir('images/square/squares_crop.jpg')));

           verify($result->width)->equals(150);
           verify($result->height)->equals(100);
       });

       $this->specify('Squares 180x180 full', function ()  {
           $this->resizer->create(new Path(codecept_data_dir('images/square180x180.jpg')));

           // TODO портит картинку. Посмотреть как можно починить шумы
           $result = $this->resizer->driver()
               ->crop(new Size(150, 100))
               ->strip()
               ->modulate()
               ->postProcessing()
               ->jpeg()
               ->save(new Path(codecept_output_dir('images/square/squares_crop_full.jpg')));

           verify($result->width)->equals(150);
           verify($result->height)->equals(100);
       });

       $this->specify('Square face 220x220 full', function ()  {
           $this->resizer->create(new Path(codecept_data_dir('images/square220x220.jpg')));

           $result = $this->resizer->driver()
               ->crop(new Size(180, 150))
               ->strip()
               ->modulate()
               ->postProcessing()
               ->jpeg()
               ->save(new Path(codecept_output_dir('images/square/squares_face_crop_full.jpg')));

           verify($result->width)->equals(180);
           verify($result->height)->equals(150);
       });

       $this->specify('Square face 1024x1024 full', function ()  {
           $this->resizer->create(new Path(codecept_data_dir('images/square1024x1024.jpg')));

           $result = $this->resizer->driver()
               ->crop(new Size(200, 200))
               ->strip()
               ->modulate()
               ->postProcessing()
               ->jpeg()
               ->save(new Path(codecept_output_dir('images/square/squares_color_crop_full.jpg')));

           verify($result->width)->equals(200);
           verify($result->height)->equals(200);
       });
   }

   public function testSquareResize()
   {
       $file = new Path(codecept_data_dir('images/square1024x1024.jpg'));

       $this->specify('Square color resize 1920x1200 full small size', function () use ($file) {
           $this->resizer->create($file);

           $result = $this->resizer->driver()
               ->resize(new Size(200, 200))
               ->strip()
               ->modulate()
               ->postProcessing()
               ->jpeg()
               ->save(new Path(codecept_output_dir('images/square/square_color_resize_full.jpg')));

           verify($result->width)->equals(200);
           verify($result->height)->equals(200);
       });

       $this->specify('Square color adaptive 1920x1200 full small size', function () use ($file) {
           $this->resizer->create($file);

           $result = $this->resizer->driver()
               ->adaptiveResize(new Size(200, 200))
               ->strip()
               ->modulate()
               ->postProcessing()
               ->jpeg()
               ->save(new Path(codecept_output_dir('images/square/square_color_adaptive_full.jpg')));

           verify($result->width)->equals(200);
           verify($result->height)->equals(200);
       });

       $this->specify('Square color marginal 1920x1200 full small size', function () use ($file) {
           $this->resizer->create($file);

           $result = $this->resizer->driver()
               ->marginalResize(new Size(200, 150))
               ->strip()
               ->modulate()
               ->postProcessing()
               ->jpeg()
               ->save(new Path(codecept_output_dir('images/square/square_color_marginal_full.jpg')));

           verify($result->width)->equals(150);
           verify($result->height)->equals(150);
       });

       $this->specify('Square face marginal 220x220 full', function () use ($file) {
           $this->resizer->create(new Path(codecept_data_dir('images/square220x220.jpg')));

           $result = $this->resizer->driver()
               ->marginalResize(new Size(150, 150))
               ->strip()
               ->jpeg()
               ->modulate()
               ->postProcessing()
               ->save(new Path(codecept_output_dir('images/square/square_face_marginal_full.jpg')));

           verify($result->width)->equals(150);
           verify($result->height)->equals(150);
       });
   }

    public function testImagick()
    {
        $this->specify('Check strip', function () {
            $this->resizer->create(new Path(codecept_data_dir('images/landscape3072x1824.jpg')));

            $striped = $this->resizer->driver()
                ->crop(new Size(1000, 1000))
                ->strip()
                ->save(new Path(codecept_output_dir('images/striped.jpg')));

            $this->resizer->create(new Path(codecept_data_dir('images/landscape3072x1824.jpg')));

            $withoutStrip = $this->resizer->driver()
                ->crop(new Size(1000, 1000))
                ->save((new Path(codecept_output_dir('images/notstriped.jpg')))->create());

            verify($striped->fileSize->bytes)->lessThan($withoutStrip->fileSize->bytes);
        });

        $this->specify('Marginal resize', function () {
            $width = 300;
            $height = 100;

            $this->resizer->create(new Path(codecept_data_dir('images/landscape400x225.jpg')));

            $result = $this->resizer->driver()
                ->marginalResize(new Size($width, $height))
                ->jpeg()
                ->strip()
                ->save((new Path(codecept_output_dir('images/marginal.jpg')))->create());

            verify($result->width)->equals($width);
            verify($result->height)->equals($height);
            verify($result->fileSize->kilobytes)->greaterThan(0);
            verify($result->fileSize->getActual())->stringContainsString('KB');
        });

        $this->specify('Square resize', function () {
            $width = $height = 100;
            $this->resizer->create(new Path(codecept_data_dir('images/square1024x1024.jpg')));

            $result = $this->resizer->driver()
                ->resize(new Size($width, $height))
                ->jpeg()
                ->strip()
                ->save((new Path(codecept_output_dir('images/square.jpg')))->create());

            verify($result->width)->equals($width);
            verify($result->height)->equals($height);
        });

        $this->specify('Modulate', function () {
            $this->resizer->create(new Path(codecept_data_dir('images/landscape3072x1824.jpg')));

            $result = $this->resizer->driver()
                ->adaptiveResize(new Size(1024, 768))
                ->modulate()
                ->save((new Path(codecept_output_dir('images/modulate.jpg')))->create());

            verify($result->fileSize->megaBytes)->lessThan(1);
        });

        $this->specify('Postprocessing', function () {
            $this->resizer->create(new Path(codecept_data_dir('images/portrait1000x1333.jpg')));

            $result = $this->resizer->driver()
                ->postProcessing()
                ->save((new Path(codecept_output_dir('images/postprocessing.jpg')))->create());

            verify($result->fileSize->megaBytes)->lessThan(1);
        });
    }
}
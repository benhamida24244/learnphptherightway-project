<?php
declare(strict_types = 1);
namespace App\Service;

class UploadDirectary
{
    private string $storagePath;
    private array $file;
    public string $filePath;
    public function __construct(string $storagePath ,  array $file){
        $this->storagePath = $storagePath;
        $this->file = $file;
    }
    public function upload():array
    {
       if(!is_dir($this->storagePath))
            {
                mkdir($this->storagePath);
            }
        $this->filePath = $this->storagePath. uniqid(). '-' . $this->file['name'];
        // File should be a CSV
        if (pathinfo($this->filePath, PATHINFO_EXTENSION) !== 'csv') {
            return [
                'success' => false,
                'message' => 'We accepted only csv file'
            ];
        }
        if(move_uploaded_file($this->file['tmp_name'],$this->filePath))
            {
                 return [
                'success' => true,
                'message' => 'Your file uploaded Successfully'
            ]; 
            }
        else 
            {
                 return [
                'success' => false,
                'message' => 'Your Have problem in uploaded system'

            ];
            }
    }   
}
<?php


use Illuminate\Database\Seeder;
use App\Models\Categories;

class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Categories::truncate();
        $residential=array('Villa','Town','house','Apartment','Land');
        $commercial=array('Office','Shop','Warehouse');
        foreach ($residential as $data) {
        	$insert['catName']=$data;
        	$insert['catType']=1;
             Categories::create($insert);
        }
        foreach ($commercial as $data) {
        	$insert['catName']=$data;
        	$insert['catType']=2;
             Categories::create($insert);
        }
    }
}

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            'Sports de raquette',
            'Sports d\'équipe',
            'Running & Trail',
            'Fitness & Musculation',
            'Sports nautiques',
            'Randonnée & Trekking',
            'Sports d\'hiver'
        ];

        foreach ($categories as $categoryTitle) {
            $category = new Categories();
            $category->setTitle($categoryTitle);
            $manager->persist($category);
        }

        $manager->flush();
    }
} 
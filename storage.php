<?php

interface IFileIO {
    function save($data);
    function load();
}

abstract class FileIO implements IFileIO {
    protected $filepath;

    public function __construct($filename) {
        if (!is_readable($filename) || !is_writable($filename)) {
            throw new Exception("Data source $filename is invalid.");
        }
        $this->filepath = realpath($filename);
    }
}

class JsonIO extends FileIO {
    public function load($assoc = true) {
        $file_content = file_get_contents($this->filepath);
        return json_decode($file_content, $assoc) ?: [];
    }

    public function save($data) {
        $json_content = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents($this->filepath, $json_content);
    }
}

class SerializeIO extends FileIO {
    public function load() {
        $file_content = file_get_contents($this->filepath);
        return unserialize($file_content) ?: [];
    }

    public function save($data) {
        $serialized_content = serialize($data);
        file_put_contents($this->filepath, $serialized_content);
    }
}

interface IStorage {
    function add($record): string;
    function findById(string $id);
    function findAll(array $params = []);
    function findOne(array $params = []);
    function update(string $id, $record);
    function delete(string $id);
    function findMany(callable $condition);
    function updateMany(callable $condition, callable $updater);
    function deleteMany(callable $condition);
}

class Car {
    public $id;
    public $brand;
    public $model;
    public $year;
    public $fuel_type;
    public $passengers;
    public $daily_price_huf;
    public $image_url;

    public function __construct($brand, $model, $year, $fuel_type, $passengers, $daily_price_huf, $image_url) {
        $this->id = uniqid();
        $this->brand = $brand;
        $this->model = $model;
        $this->year = $year;
        $this->fuel_type = $fuel_type;
        $this->passengers = $passengers;
        $this->daily_price_huf = $daily_price_huf;
        $this->image_url = $image_url;
    }
}

class User {
    public $full_name;
    public $email;
    public $password;
    public $admin_status;

    public function __construct($full_name, $email, $password, $admin_status = false) {
        $this->full_name = $full_name;
        $this->email = $email;
        $this->password = $password;
        $this->admin_status = $admin_status;
    }
}

class Booking {
  public $id;
    public $start_date;
    public $end_date;
    public $user_email;
    public $car_id;

    public function __construct($start_date, $end_date, $user_email, $car_id) {
        
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->user_email = $user_email;
        $this->car_id = $car_id;
    }
}

class Storage implements IStorage {
    protected $contents;
    protected $io;

    public function __construct(IFileIO $io, $assoc = true) {
        $this->io = $io;
        $this->contents = (array)$this->io->load($assoc);
    }

    public function __destruct() {
        $this->io->save($this->contents);
    }

    public function add($record): string {
      if ($record instanceof User) {
         
          $this->contents[] = $record; 
          return $record->email; 
      } else {
          
          $id = uniqid();
          if (is_array($record)) {
              $record['id'] = $id;
          } else if (is_object($record)) {
              $record->id = $id;
          }
          $this->contents[$id] = $record;
          return $id;
      }
  }
  

    public function addCar($brand, $model, $year, $fuel_type, $passengers, $daily_price_huf, $image_url): string {
        $car = new Car($brand, $model, $year, $fuel_type, $passengers, $daily_price_huf, $image_url);
        return $this->add($car);
    }

    public function findCarById($id) {
        return $this->contents[$id] ?? null;
    }

    public function addUser($full_name, $email, $password, $admin_status = false): string {
        $user = new User($full_name, $email, $password, $admin_status);
        return $this->add($user);
    }


    public function findUserByEmail($email) {

        $filtered_users = array_filter($this->contents, function($content) use ($email) {
            return $content instanceof User && strtolower($content->email) === strtolower($email);
        });
        
        return !empty($filtered_users) ? array_values($filtered_users)[0] : null;
    }
    

    
    public function addBooking($start_date, $end_date, $user_email, $car_id): string {
        $booking = new Booking($start_date, $end_date, $user_email, $car_id);
        return $this->add($booking);
    }

    public function findBookingsByUserEmail($email) {
        return array_filter($this->contents, function ($content) use ($email) {
            return $content instanceof Booking && $content->user_email === $email;
        });
    }

    public function findById(string $id) {
        return $this->contents[$id] ?? NULL;
    }

    public function findAll(array $params = []) {
        return array_filter($this->contents, function ($item) use ($params) {
            foreach ($params as $key => $value) {
                if (((array)$item)[$key] !== $value) {
                    return FALSE;
                }
            }
            return TRUE;
        });
    }

    public function findOne(array $params = []) {
        $found_items = $this->findAll($params);
        $first_index = array_keys($found_items)[0] ?? NULL;
        return $found_items[$first_index] ?? NULL;
    }

    public function update(string $id, $record) {
        $this->contents[$id] = $record;
    }

    public function delete(string $id) {
        unset($this->contents[$id]);
    }

    public function findMany(callable $condition) {
        return array_filter($this->contents, $condition);
    }

    public function updateMany(callable $condition, callable $updater) {
        array_walk($this->contents, function (&$item) use ($condition, $updater) {
            if ($condition($item)) {
                $updater($item);
            }
        });
    }

    public function deleteMany(callable $condition) {
        $this->contents = array_filter($this->contents, function ($item) use ($condition) {
            return !$condition($item);
        });
    }
}



// // Add a new Car
// $car_id = $storage->addCar('Toyota', 'Corolla', 2020, 'Gasoline', 5, 4500, 'https://example.com/toyota-corolla.jpg');

// // Add a new User
// $user_id = $storage->addUser('John Doe', 'johndoe@example.com', 'securepassword123', true);

// // Add a new Booking for the user and car
// $booking_id = $storage->addBooking('2025-01-15', '2025-01-20', 'johndoe@example.com', $car_id);

// // Find a car by ID
// $car = $storage->findCarById($car_id);

// // Find a user by email
// $user = $storage->findUserByEmail('johndoe@example.com');

// // Find all bookings for a user
// $bookings = $storage->findBookingsByUserEmail('johndoe@example.com');

// // Output Example Data
// echo "User: " . $user->full_name . " (" . $user->email . ")\n";
// echo "Bookings for " . $user->email . ":\n";
// foreach ($bookings as $booking) {
//     echo "- Car Model: " . $car->model . " (" . $car->brand . "), from " . $booking->start_date . " to " . $booking->end_date . "\n";
// }

?>

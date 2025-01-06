<?php
require_once '../classes/reviewclass.php';
require_once '../classes/clientclasse.php';

$review = new Review();
$clients = $review->getClientsInfo();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Reviews - AutoLoc</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">All Reviews</h1>
        
        <section id="manage-users" class="section-content">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="text-xl font-semibold">Manage Clients</h2>
                </div>
                <div class="p-6">
                    <table class="w-full">
                        <thead>
                            <tr>
                                <th class="text-left p-3">Client Name</th>
                                <th class="text-left p-3">Email</th>
                                <th class="text-left p-3">Account Created</th>
                                <th class="text-left p-3">Number of Reservations</th>
                                <th class="text-left p-3">Reserved Cars</th>
                                <th class="text-left p-3">Number of Reviews</th>
                                <th class="text-left p-3">Archive</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clients as $client): ?>
                                <tr>
                                    <td class="p-3"><?php echo htmlspecialchars($client['firstName'] . ' ' . $client['lastName']); ?></td>
                                    <td class="p-3"><?php echo htmlspecialchars($client['email']); ?></td>
                                    <td class="p-3"><?php echo htmlspecialchars($client['createdAt']); ?></td>
                                    <td class="p-3"><?php echo htmlspecialchars($client['num_reservations']); ?></td>
                                    <td class="p-3"><?php echo htmlspecialchars($client['reserved_cars']); ?></td>
                                    <td class="p-3"><?php echo htmlspecialchars($client['num_reviews']); ?></td>
                                    <td class="p-3">
                                        <form action="Archiveruser.php" method="POST">
                                            <input type="hidden" name="clientId" value="<?php echo $client['id']; ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-800">Archive</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
    </div>
</body>
</html>
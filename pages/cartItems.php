<tr id="<?php echo $product['arrayID']; ?>">
    <td data-th="Product">
        <div class="row">
            <div class="col-md-3 text-left">
                <?php
                    if (strlen($product['productImage']) < 80) {
                        $imagePath = ($product['productImage']);
                        } else {
                            $imagePath = 'data:image/jpeg;base64,' . base64_encode($product['productImage']);
                        }
                ?>
                <img src="<?= $imagePath ?>" alt="<?= $product['productName'] ?>" class="img-fluid d-none d-md-block rounded mb-2 shadow">
            </div>
            <div class="col-md-9 text-left mt-sm-2">
                <h5 class="text-uppercase"><?php echo $product['productName']; ?></h5>
                <p class="font-weight-light"><?php echo 'Size: ' . $product['userSelectedSize']; ?></p>
            </div>
        </div>
    </td>
    <td data-th="Price">$<?php echo $product['productPrice']; ?></td>
    <td data-th="Quantity"><?php echo $product['userSelectedQuantity'] ?></td>
    <td class="actions" data-th="">
        <div class="text-right">
            <form method="post" action="deleteCartItem.php">
                <input type="hidden" name="productIndex" value="<?php echo $product['arrayID']; ?>">
                <button type="submit" class="btn btn-white border-secondary bg-white btn-md mb-2">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
        </div>
    </td>
</tr>
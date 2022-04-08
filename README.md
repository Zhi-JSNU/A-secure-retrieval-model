# A secure retrieval model
Cloud storage service allows users to store data in a remote server, enabling services such as data backup, data confidentiality, and dynamic storage space. However, ensuring the integrity and security of data is a concern for most users. Thus, some users encrypt their data and store them in a cloud server. In this case, some useful functions (retrieval, sharing, etc.) will be affected. Additionally, increasing users not only pay attention to the security of data but also focus on protecting their data usage habits, such as the protection of retrieval information. To this end, in this study, we propose a cloud service-oriented secure retrieval model by designing an index file and introducing a third-party trusted server to manage and update the index file, which realizes the protection of retrieval information and the sharing of encrypted data.
we propose a secure retrieval model for cloud storage service (CSS). Its purpose is to retrieve encrypted data stored on the CS and ensure the hiding of the retrieval information to prevent CSPs and third parties from obtaining the retrieval information and improve the protection of data privacy. In this model, we introduce a management server between the user and CSP, which is mainly responsible for the retrieval and uploading functions. The management server is a trusted server, and the retrieval function is based on metadata. We designed an index file to manage such metadata. The metadata includes file information, directory information, keywords, owners, and file storage addresses on the CS. To prevent the CS from obtaining the file information by the corresponding file name, the file name will be replaced by random bytes before uploading to the CS. Index files are stored in a semiciphertext format. 
To realize the CP-ABE-based secure retrieval model for CSS, we wrote the client, management server, and CS system programs in PHP and invoked the CP-ABE toolkit (version xx) to implement the encryption and decryption functions of the management server.

# Format design of the index file

![1](https://user-images.githubusercontent.com/103243686/162415617-65b14f64-4c56-41be-b6d5-81a5d88bfe09.png)
The top part represents the plaintext, which we call the Header. The other part represents the ciphertext. The ciphertext part comprises different blocks, and each block corresponds to a decryption attribute. When the user searches, the management server decrypts the block according to the user’s attribute and performs retrieval simultaneously. The retrieval process is as follows. It first locates the byte storage range of the block corresponding to the user’s attribute in the Header, accurately extracts the ciphertext of the corresponding block, and then performs the decryption action. When there are subfolders in this block, the management server needs to locate the index files corresponding to the subfiles and then repeat the same process.

# Secure retrieval model based on CP-ABE
CP-ABE is a novel public key cryptography for data sharing. It generates a user’s private key by associating an attribute represented as a string with an arbitrary random number. When encrypting data, the user specifies attribute access rights for the ciphertext. When decrypting, provided the ciphertext access structure is satisfied, the ciphertext can be decrypted. The secure retrieval model based on CP-ABE is designed as follows:
1. Key Generation Center (KGC): KGC manages user IDs and attributes, generates keys used in CP-ABE, and distributes them to ordinary users and trusted third-party servers (management servers). Because it manages all private and public keys, it has powerful authority to decrypt all ciphertexts. In the proposed model, KGC initializes and generates the master key.
SetUp: This algorithm chooses a bilinear group G0 of prime order p with generator g and then chooses two random exponents α，β∈Zp. The public key is expressed as follows:
PK = G0, g, h = gβ, f = g1/β, e(g,g)α
The Master Key is given by：
MK = (β, gα)
2. User: Users have their IDs and attributes, and the search is not performed through CSPs but through trusted third-party servers (management servers). Processing, such as uploading, downloading, and searching is performed through the browser. The private key SK with embedded attributes can be generated based on the master key obtained by the KGC. The client (user) can generate and encrypt the private key.
KeyGen(MK,S): Key generation algorithm takes input as a set of attributes S and outputs a key that identifies with that set. The algorithm first chooses a random r∈Zp, and then, a random rj∈Zp for each attribute j ∈ S. The key is computed as follows:
SK = ( D = g(α+r)/β, ∀j∈S: Dj = gr·H(j)rj ,D’j = grj )
Encrypt(PK,M, T ): The encryption algorithm encrypts a message M under the tree access structure T. It first chooses a polynomial qx for each node x (including the leaves) in the tree T. This polynomial is chosen in a top-down manner, starting from the root node R. Then, the algorithm chooses a random S ∈ Zp and sets qR（0） = S. Then, it chooses dR other points of the polynomial qR randomly to define it completely. For any other node x, it sets qx(0) = qparent(x) and chooses dx other points randomly to completely define qx. Let Y be the set of leaf nodes in T. The ciphertext can be generated as follows: 
CT = ( T , C˜ = Me(g,g)αs, C = hs , ∀y ∈ Y : Cy = g qy(0), C′ y = H(att(y))qy(0))
3. Management Server: The management server represents a trusted third-party server. It will respond to user requests and maintain index files. The management server can decrypt index files (corresponding blocks), but cannot decrypt the data files stored on the CS. When receiving a user’s request, it will first confirm the user’s attributes and then perform decryption and retrieval based on the search request within the scope of the user’s attribute authority.
Decrypt(CT, SK): The decryption algorithm starts by calling the root node R of the access structure T. If the attribute set S is satisfied, it can be set as A = DecryptNode(CT, SK, r) = e(g, g)rqR(0) = e(g, g) rs。The plaintext M can be generated as follows:
M = C˜/ (e(C, D)/A) = C˜/ e (hs , g(α+r)/β)/e(g, g)rs)
4. CS: CS provides data storage and other functions for ordinary users. The ciphertext data and index files cannot be decrypted by the CS because the private key is not retained.
The core processing process of the proposed retrieval model based on CP-ABE is mainly divided into uploading and searching.
![2](https://user-images.githubusercontent.com/103243686/162416162-df8abc60-3ce0-45b7-9f8d-cd5b30b3fe2e.png)
# Update process of the index file
In the CP-ABE-based model, to prevent prying eyes from the CSP, the decryption behavior will rely on the trusted management server. The management server first downloads the semiciphertext index file from the CS, confirms the user’s attribute, and then decrypts and retrieves the corresponding block. The system has functions such as uploading, updating, deleting, creating files, and searching. All data and retrieval information during operations are invisible to CSPs.
The update process of the index file is as follows.
1. When a user wants to create a folder in the current directory, the management server will download the latest root index file from the CS, then locates the index file corresponding to the current folder through the root index file according to the user’s attributes (permissions), and creates a new index file at the corresponding location (refer to the “ChildFolder” part in Figure 1) of the corresponding block in the current index file, including the name and address (link) of the index file. This process must repeatedly decrypt and locate according to the user’s attributes and finally update the corresponding index file. Additionally, to prevent other users from updating data simultaneously, exclusive operations will be implemented in this process.
2. When a user uploads a new file in the current directory, the management server will download the latest root index file from the CS and then locate the index file corresponding to the current folder based on the root index file according to the user’s attributes and permissions. Next, the management server uploads the file to the CS instead of the user and replaces the file name simultaneously. The purpose is to hide the file name information and secure the data. Then, the management server judges according to the attribute information provided by the user when uploading the file. 1) If the attribute already exists in the current index directory file, add a new line of information to the file position (refer to the file “F” part in Figure 1) of the block corresponding to the attribute of the current index directory, including the original file name, randomly generated file name (corresponding to the storage address on the CS), decryption attribute of the file, the file owner, and keywords for retrieval. 2) If the attribute is not found in the current index file, the management server needs to update the Header and create a new block for this attribute. All keywords are set by the user. This process also needs to repeatedly perform decryption and location according to the user’s attributes and finally update the corresponding index file. Meanwhile, to prevent other users from updating data simultaneously, exclusive operations will be implemented.
3. When a user performs a delete operation, the processing method is similar to the above. It is necessary to locate the index file corresponding to the current folder and then delete the corresponding information.

![3](https://user-images.githubusercontent.com/103243686/162416424-f3f62d54-9671-4c53-9fdc-4b11cd9f1a9b.png)

# Invoke CPABE toolkit
The codes of CS, client and management server have been written. What you need to do now is to call the cpabe toolkit. The default address is the CPABE folder.













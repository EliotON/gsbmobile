import React, { useState, useEffect } from 'react';
import "./global.css";
import { View, Text, TextInput, Button, Alert, Image, TouchableOpacity } from 'react-native';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { NavigationContainer } from '@react-navigation/native';
import { createStackNavigator } from '@react-navigation/stack';
import VisitsPage from './pages/VisitsPage'; // Import the new VisitsPage
import AddVisitPage from './pages/AddVisitPage'; // Import the new AddVisitPage
import { Ionicons, MaterialIcons, FontAwesome5 } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';

const Stack = createStackNavigator();

export default function App() {
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [isLoggedIn, setIsLoggedIn] = useState(false);

  useEffect(() => {
    const checkToken = async () => {
      const token = await AsyncStorage.getItem('authToken');
      if (token) {
        setIsLoggedIn(true);
      }
    };
    checkToken();
  }, []);

  const handleLogin = async () => {
    try {
      const response = await fetch('https://s5-4242.nuage-peda.fr/gsbapieliot/API/ApiAuth.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ email, password }),
      });

      const data = await response.json();

      if (response.ok) {
        Alert.alert('Succès', 'Connexion réussie');
        await AsyncStorage.setItem('authToken', data.token); // Store the token
        setIsLoggedIn(true);
      } else {
        Alert.alert('Erreur', data.error || 'Erreur de connexion');
      }
    } catch (error) {
      Alert.alert('Erreur', 'Impossible de se connecter au serveur');
    }
  };

  const handleLogout = async () => {
    await AsyncStorage.removeItem('authToken'); // Remove the token
    setIsLoggedIn(false);
  };

  return (
    <NavigationContainer>
      <Stack.Navigator>
        {!isLoggedIn ? (
          <Stack.Screen name="Login" options={{ headerShown: false }}>
            {() => (
              <View className="flex-1 bg-white">
                <View className="flex-1 ">
                  <LinearGradient
                    colors={['#3B82F6', '#2563EB']}
                    className="px-8 pt-16 pb-10 rounded-b-3xl shadow-lg"
                  >
                    <View className="items-center mt-28">
                      <View className="shadow-lg rounded-full">
                        <Image 
                          source={require('./public/logo.png')}
                          className="h-20 w-20 rounded-full border-4 border-white"
                          resizeMode="cover"
                        />
                      </View>
                    </View>
                    <Text className="text-3xl font-bold text-center text-white mb-2">
                      Connexion GSB
                    </Text>
                    <Text className="text-center text-blue-100 mb-6">
                      Espace réservé aux visiteurs
                    </Text>
                  </LinearGradient>

                  <View className="px-8 pt-8">
                    <View className="space-y-4 w-full">
                      <TextInput
                        className="w-full bg-gray-50 border border-gray-200 rounded-xl p-4 text-gray-900 mb-4"
                        placeholder="Email"
                        value={email}
                        onChangeText={setEmail}
                        keyboardType="email-address"
                        placeholderTextColor="#9CA3AF"
                      />
                      <TextInput
                        className="w-full bg-gray-50 border border-gray-200 rounded-xl p-4 text-gray-900 mb-4"
                        placeholder="Mot de passe"
                        value={password}
                        onChangeText={setPassword}
                        secureTextEntry
                        placeholderTextColor="#9CA3AF"
                      />
                      <TouchableOpacity 
                        className="w-full bg-blue-600 rounded-xl p-4 shadow-md"
                        onPress={handleLogin}
                      >
                        <Text className="text-white text-center font-semibold text-lg">
                          Se connecter
                        </Text>
                      </TouchableOpacity>
                    </View>
                  </View>
                </View>
              </View>
            )}
          </Stack.Screen>
        ) : (
          <>
            <Stack.Screen name="Home" options={{ headerShown: false }}>
              {({ navigation }) => (
                <View className="flex-1 bg-white mb-10">
                  <LinearGradient
                    colors={['#3B82F6', '#2563EB']}
                    className="px-8 pt-16 pb-10 rounded-b-3xl shadow-lg"
                  >
                    <View className="items-center mt-28">
                      <View className="shadow-lg rounded-full">
                        <Image 
                          source={require('./public/logo.png')}
                          className="h-20 w-20 rounded-full border-4 border-white"
                          resizeMode="cover"
                        />
                      </View>
                    </View>
                    <Text className="text-3xl font-bold text-center text-white mb-2">
                      Tableau de bord
                    </Text>
                    <Text className="text-center text-blue-100 mb-2">
                      Bienvenue sur votre espace GSB
                    </Text>
                  </LinearGradient>

                  <View className="px-8 pt-8 flex-1">
                    <Text className="text-xl font-semibold text-gray-700 mb-6">
                      Que souhaitez-vous faire ?
                    </Text>
                    <View className="space-y-5">
                      <TouchableOpacity 
                        className="bg-white rounded-xl p-5 flex-row items-center shadow-md border border-gray-100 mb-4"
                        onPress={() => navigation.navigate('Visits')}
                      >
                        <View className="bg-blue-500 p-3 rounded-lg mr-4">
                          <FontAwesome5 name="clipboard-list" size={24} color="white" />
                        </View>
                        <View>
                          <Text className="text-gray-800 text-lg font-semibold">Voir les visites</Text>
                          <Text className="text-gray-500">Consultez votre historique</Text>
                        </View>
                      </TouchableOpacity>
                      
                      <TouchableOpacity 
                        className="bg-white rounded-xl p-5 flex-row items-center shadow-md border border-gray-100 mb-4"
                        onPress={() => navigation.navigate('AddVisit')}
                      >
                        <View className="bg-green-500 p-3 rounded-lg mr-4">
                          <Ionicons name="add-circle" size={24} color="white" />
                        </View>
                        <View>
                          <Text className="text-gray-800 text-lg font-semibold">Ajouter une visite</Text>
                          <Text className="text-gray-500">Enregistrer une nouvelle visite</Text>
                        </View>
                      </TouchableOpacity>
                      
                      <TouchableOpacity 
                        className="bg-white rounded-xl p-5 flex-row items-center shadow-md border border-gray-100 mb-4"
                        onPress={handleLogout}
                      >
                        <View className="bg-red-500 p-3 rounded-lg mr-4">
                          <MaterialIcons name="logout" size={24} color="white" />
                        </View>
                        <View>
                          <Text className="text-gray-800 text-lg font-semibold">Se déconnecter</Text>
                          <Text className="text-gray-500">Quitter votre session</Text>
                        </View>
                      </TouchableOpacity>
                    </View>
                  </View>
                </View>
              )}
            </Stack.Screen>
            <Stack.Screen name="Visits" component={VisitsPage} />
            <Stack.Screen name="AddVisit" component={AddVisitPage} />
          </>
        )}
      </Stack.Navigator>
    </NavigationContainer>
  );
}
